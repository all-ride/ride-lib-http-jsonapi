<?php

namespace ride\library\http\jsonapi\exception;

/**
 * Exception thrown when a request parameter is invalid
 */
class BadRequestJsonApiException extends JsonApiException {

    /**
     * Name of the referenced parameter
     * @var string
     */
    private $parameter;

    /**
     * Type of the unsupported resource
     * @var string
     */
    private $resource;

    /**
     * Sets the referenced parameter
     * @param string $parameter Name of the parameter
     * @return null
     */
    public function setParameter($parameter) {
        $this->parameter = $parameter;
    }

    /**
     * Gets the referenced parameter
     * @return string Name of the parameter
     */
    public function getParameter() {
        return $this->parameter;
    }

    /**
     * Sets the unsupported resource
     * @param string $resource Type of the unsupported resource
     * @return null
     */
    public function setResource($resource) {
        $this->resource = $resource;
    }

    /**
     * Gets the unsupported resource
     * @return string Type of the unsupported source
     */
    public function getResource() {
        return $this->resource;
    }

}
