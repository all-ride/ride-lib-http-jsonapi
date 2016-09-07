<?php

namespace ride\library\http\jsonapi;

use ride\library\http\jsonapi\exception\JsonApiException;

use \JsonSerializable;

/**
 * Relationship of a JSON API resource
 */
class JsonApiRelationship extends AbstractLinkedJsonApiElement implements JsonSerializable {

    /**
     * Data of this relationship
     * @var JsonApiResource|array|boolean
     */
    protected $data = false;

    /**
     * Sets a resource collection as data of this document
     * @param array $collection Array of data objects for the adapter of the
     * resource type
     * @return null
     * @see JsonApiResourceAdapter
     */
    public function setResourceCollection(array $collection) {
        foreach ($collection as $index => $data) {
            if (!$data instanceof JsonApiResource) {
                throw new JsonApiException('Could not set resource collection: item with key ' . $index . ' is not a JsonApiResource');
            }
        }

        $this->data = array_values($collection);
    }

    /**
     * Sets a single resource as data of this document
     * @param array $data Data object for the adapter of the resource type
     * @return null
     * @see JsonApiResourceAdapter
     */
    public function setResource(JsonApiResource $resource = null) {
        $this->data = $resource;
    }

    /**
     * Gets the set data
     * @return boolean|JsonApiResource|array
     * @see setResourceCollection
     * @see setResource
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Specifies the data which should be serialized to JSON
     * @return scalar
     */
    public function jsonSerialize() {
        if (!$this->links && $this->data === false && !$this->meta) {
            throw new JsonApiException('Could not get json value: A relationship MUST contain at least links, data or a meta top-level member');
        }

        $value = array();

        if ($this->links) {
            $value['links'] = $this->links;
        }

        if ($this->data !== false) {
            if (is_array($this->data)) {
                $value['data'] = array();
                foreach ($this->data as $data) {
                    $value['data'][] = $data->getJsonValue(false);
                }
            } elseif ($this->data) {
                $value['data'] = $this->data->getJsonValue(false);
            } else {
                $value['data'] = null;
            }
        }

        if ($this->meta) {
            $value['meta'] = $this->meta;
        }

        return $value;
    }

}
