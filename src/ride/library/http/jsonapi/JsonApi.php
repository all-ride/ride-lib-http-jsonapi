<?php

namespace ride\library\http\jsonapi;

use ride\library\http\jsonapi\exception\JsonApiException;

/**
 * Container for a JSON API implementation
 */
class JsonApi {

    /**
     * Content type for a JSON API request and response
     * @var string
     */
    const CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Adapters for the different resource types
     * @var array
     */
    protected $resourceAdapters = array();

    /**
     * Sets multiple resource adapters at once
     * @param array $resourceAdapters Array with the type as key and the
     * instance of the resource adapter as value
     * @return null
     * @see setResourceAdapter
     */
    public function setResourceAdapters(array $resourceAdapters) {
        foreach ($resourceAdapters as $type => $resourceAdapter) {
            $this->setResourceAdapter($type, $resourceAdapter);
        }
    }

    /**
     * Sets a resource adapter for the provided type
     * @param string $type Name of the resource type
     * @param JsonApiResourceAdapter $resourceAdapter Resource adapter for the
     * provided type
     * @return null
     */
    public function setResourceAdapter($type, JsonApiResourceAdapter $resourceAdapter) {
        $this->resourceAdapters[$type] = $resourceAdapter;
    }

    /**
     * Gets the resource adapter for the provided type
     * @param string $type Name of the resource type
     * @return JsonApiResourceAdapter
     * @throws \ride\library\http\jsonapi\exception\JsonApiException when no
     * resource adapter is set for the provided type
     */
    public function getResourceAdapter($type) {
        if (!isset($this->resourceAdapters[$type])) {
            throw new JsonApiException('Could not get resource adapter: no adapter set for type ' . $type);
        }

        return $this->resourceAdapters[$type];
    }

    /**
     * Creates a document query
     * @param array $parameters Query parameters eg. $_GET
     * @return JsonApiQuery
     */
    public function createQuery(array $parameters) {
        return new JsonApiQuery($parameters);
    }

    /**
     * Creates a new document
     * @param JsonApiQuery $query Query of the document
     * @return JsonApiDocument
     */
    public function createDocument(JsonApiQuery $query = null) {
        $document = new JsonApiDocument();
        $document->setApi($this);
        if ($query) {
            $document->setQuery($query);
        }

        return $document;
    }

    /**
     * Creates a new resource
     * @param string $type Name of the resource type
     * @param string $id Id of the resource
     * @param string $relationshipPath dot-separated list of the resource path
     * @return JsonApiResource
     */
    public function createResource($type, $id, $relationshipPath = null) {
        $resource = new JsonApiResource($type, $id);
        $resource->setRelationshipPath($relationshipPath);

        return $resource;
    }

    /**
     * Creates a new relationship
     * @return JsonApiRelationship
     */
    public function createRelationship() {
        return new JsonApiRelationship();
    }

    /**
     * Creates a new error
     * @param string $statusCode HTTP status code applicable to this problem
     * @param string $code Application-specific error code
     * @param string $title A short, human-readable summary of the problem that
     * SHOULD NOT change from occurrence to occurrence of the problem, except
     * for purposes of localization.
     * @param string $detail A human-readable explanation specific to this
     * occurrence of the problem.
     * @return JsonApiError
     */
    public function createError($statusCode = null, $code = null, $title = null, $detail = null) {
        $error = new JsonApiError();
        $error->setStatusCode($statusCode);
        $error->setCode($code);
        $error->setTitle($title);
        $error->setDetail($detail);

        return $error;
    }

}
