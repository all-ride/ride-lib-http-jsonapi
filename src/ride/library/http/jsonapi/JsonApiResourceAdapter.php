<?php

namespace ride\library\http\jsonapi;

/**
 * Interface for an adapter of data from your model to an API resource
 */
interface JsonApiResourceAdapter {

    /**
     * Gets a resource instance for the provided model data
     * @param mixed $data Data to adapt
     * @param JsonApiDocument $document Requested document
     * @return JsonApiResource
     */
    public function getResource($data, JsonApiDocument $document);

}
