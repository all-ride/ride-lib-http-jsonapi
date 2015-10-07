<?php namespace ride\library\http\jsonapi;

use \JsonSerializable;

/**
 * Container for a JSON API document
 */
class JsonApiDocument extends AbstractLinkedJsonApiElement implements JsonSerializable {

    /**
     * API definition
     * @var JsonApi
     */
    protected $api;

    /**
     * Received query
     * @var JsonApiQuery
     */
    protected $query;

    /**
     * HTTP status code for this document
     * @var string
     */
    protected $statusCode;

    /**
     * Errors on the document
     * @var array
     */
    protected $errors = array();

    /**
     * Data of the document
     * @var JsonApiResource|array|boolean
     */
    protected $data = false;

    /**
     * Included resources for a compound document
     * @var array
     */
    protected $included = array();

    /**
     * Index of the assigned resources
     * @var array
     */
    protected $index = array();

    /**
     * Gets a string representation of this document
     * @return string
     */
    public function __toString() {
        return json_encode($this->getJsonValue());
    }

    /**
     * Specifies the data which should be serialized to JSON
     * @return scalar
     */
    public function jsonSerialize() {
        return $this->getJsonValue();
    }

    /**
     * Sets the instance of the API
     * @param JsonApi $api
     * @return null
     */
    public function setApi(JsonApi $api) {
        $this->api = $api;
    }

    /**
     * Gets the instance of the API
     * @return JsonApi
     */
    public function getApi() {
        return $this->api;
    }

    /**
     * Sets the received query
     * @param JsonApiQuery $query
     * @return null
     */
    public function setQuery(JsonApiQuery $query) {
        $this->query = $query;
    }

    /**
     * Gets the received query
     * @return JsonApiQuery
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Sets the HTTP status code for this document
     * @param string $statusCode Status code
     * @return null
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
    }

    /**
     * Gets the HTTP status code for this document
     * @return string
     */
    public function getStatusCode() {
        if ($this->statusCode) {
            return $this->statusCode;
        } elseif (!$this->hasContent()) {
            return 204; // no content
        } elseif ($this->errors) {
            foreach ($this->errors as $error) {
                if ($error->getStatusCode()) {
                    return $error->getStatusCode();
                }
            }

            return 400; // bad request
        } else {
            return 200; // ok
        }
    }

    /**
     * Checks if this document has content
     * @return boolean
     */
    public function hasContent() {
        return $this->errors || $this->meta || $this->data !== false;
    }

    /**
     * Adds an error to this document
     * @param JsonApiError $error Error to add
     * @return null
     */
    public function addError(JsonApiError $error) {
        $this->errors[] = $error;

        if (!$this->statusCode && $error->getStatusCode()) {
            $this->statusCode = $error->getStatusCode();
        }
    }

    /**
     * Gets the errors of this document
     * @return array
     * @see JsonApiError
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Sets a resource collection as data of this document
     * @param string $type Name of the resource type
     * @param array $collection Array of data objects for the adapter of the
     * resource type
     * @return null
     * @see JsonApiResourceAdapter
     */
    public function setResourceCollection($type, array $collection) {
        foreach ($collection as $index => $data) {
            if (!$data instanceof AbstractJsonApiElement) {
                $data = $this->adaptResource($type, $data);
            }


            $collection[$index] = $data;

            $this->indexResource($data);
            $this->includeRelationships($data->getRelationships());
        }

        $this->data = array_values($collection);
        $this->errors = false;
    }

    /**
     * Sets a single resource as data of this document
     * @param string $type Name of the resource type
     * @param array $data Data object for the adapter of the resource type
     * @return null
     * @see JsonApiResourceAdapter
     */
    public function setResourceData($type, $data) {
        if (!$data instanceof AbstractJsonApiElement) {
            $data = $this->adaptResource($type, $data);
        }

        $this->indexResource($data);

        $this->data = $data;
        $this->errors = false;

        // set the resource links as document links
        $this->parseDataLinks();

        // add included resources from the relationships
        if ($data !==  null) {
            $this->includeRelationships($data->getRelationships());
        }
    }

    /**
     * Sets the provided relationship as data of this document
     * @param JsonApiRelationship $relationship
     * @return null
     */
    public function setRelationshipData(JsonApiRelationship $relationship) {
        $this->data = $relationship->getData();

        $this->parseDataLinks();
    }

    /**
     * Parses the data links into the document
     * @return null
     */
    protected function parseDataLinks() {
        if (!$this->data instanceof AbstractLinkedJsonApiElement) {
            return;
        }

        $links = $this->data->getLinks();
        if (!$links) {
            return;
        }

        foreach ($links as $name => $link) {
            $this->setLink($name, $link);
        }

        $this->data->clearLinks();
    }

    /**
     * Adapts the data to a API resource
     * @param string $type Name of the resource type
     * @param mixed $data Data object to adapt
     * @return JsonApiResource
     */
    protected function adaptResource($type, $data) {
        $resourceAdapter = $this->api->getResourceAdapter($type);

        return $resourceAdapter->getResource($data, $this);
    }

    /**
     * Includes the provided relationships
     * @param array $relationships Relationships from a resource
     * @return null
     * @see JsonApiResource
     */
    protected function includeRelationships(array $relationships) {
        foreach ($relationships as $relationship) {
            $data = $relationship->getData();
            if (is_array($data)) {
                foreach ($data as $resource) {
                    $this->addIncluded($resource);
                }
            } elseif ($data !== null) {
                $this->addIncluded($data);
            }
        }
    }

    /**
     * Adds a included resource for a compound document
     * @param JsonApiResource $resource
     * @return boolean True when added, false if not
     */
    public function addIncluded(JsonApiResource $resource) {
        $type = $resource->getType();
        $id = $resource->getId();
        $relationships = $resource->getRelationships();

        if (isset($this->index[$type][$id]) || (!$resource->getAttributes() && !$relationships)) {
            return false;
        }

        $this->indexResource($resource);

        if (!isset($this->included[$type])) {
            $this->included[$type] = array();
        }

        $this->included[$type][$id] = $resource;

        $relationshipPath = $resource->getRelationshipPath();
        foreach ($relationships as $name => $relationship) {
            $fieldRelationshipPath = ($relationshipPath ? $relationshipPath . '.' : '') . $name;

            if (!$this->query->isIncluded($fieldRelationshipPath)) {
                continue;
            }

            $data = $relationship->getData();
            if (is_array($data)) {
                foreach ($data as $d) {
                    $this->addIncluded($d);
                }
            } elseif ($data) {
                $this->addIncluded($data);
            }
        }

        return true;
    }

    /**
     * Indexes the provided resource to we can catch when it's added again
     * @param JsonApiResource $resource
     * @return null
     */
    protected function indexResource(JsonApiResource $resource = null) {
        if ($resource === null) {
            return;
        }

        if (!isset($this->index[$resource->getType()])) {
            $this->index[$resource->getType()] = array();
        }

        $this->index[$resource->getType()][$resource->getId()] = true;
    }

    /**
     * Gets a representation of this element ready for JSON encoding
     * @param boolean $full Flag to see if the full version should be returned
     * @return scalar
     */
    public function getJsonValue($full = true) {
        if ($this->data === false && !$this->errors && !$this->meta) {
            throw new JsonApiException('Could not get json value: A document MUST contain at least the data, errors or meta top-level member');
        }

        $value = array(
            'jsonapi' => array('version' => '1.0'),
        );

        if ($this->links) {
            $value['links'] = $this->links;
        }


        if ($this->errors) {
            $value['errors'] = $this->errors;
        } elseif ($this->data !== false) {
            if (is_array($this->data)) {
                $value['data'] = array();
                foreach ($this->data as $data) {
                    $value['data'][] = $data->getJsonValue(true);
                }
            } else {
                $value['data'] = $this->data->getJsonValue(true);
            }

            if ($this->included) {
                $value['included'] = array();

                foreach ($this->included as $type => $resources) {
                    foreach ($resources as $resource) {
                        $value['included'][] = $resource->getJsonValue(true);
                    }
                }
            }
        }

        if ($this->meta) {
            $value['meta'] = $this->meta;
        }

        return $value;
    }

}
