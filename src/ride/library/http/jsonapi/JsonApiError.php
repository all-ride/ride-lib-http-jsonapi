<?php

namespace ride\library\http\jsonapi;

use ReturnTypeWillChange;
use ride\library\http\jsonapi\exception\JsonApiException;

use \JsonSerializable;

/**
 * Data container for an error
 */
class JsonApiError extends AbstractLinkedJsonApiElement implements JsonSerializable {

    /**
     * A unique identifier for this particular occurrence of the problem
     * @var string
     */
    protected $id;

    /**
     * HTTP status code applicable to this problem
     * @var string
     */
    protected $statusCode;

    /**
     * Application-specific error code
     * @var string
     */
    protected $code;

    /**
     * A short, human-readable summary of the problem that SHOULD NOT change
     * from occurrence to occurrence of the problem, except for purposes of
     * localization.
     * @var string
     */
    protected $title;

    /**
     * A human-readable explanation specific to this occurrence of the problem.
     * @var string
     */
    protected $detail;

    /**
     * A JSON Pointer [RFC6901] to the associated entity in the request document
     * [e.g. "/data" for a primary data object, or "/data/attributes/title" for
     * a specific attribute].
     * @var string
     */
    protected $sourcePointer;

    /**
     * A string indicating which query parameter caused the error.
     * @var string
     */
    protected $sourceParameter;

    /**
     * Sets the id of this error
     * @param string $id
     * @return null
     */
    public function setId($id) {
        if ($id !== null && !is_numeric($id) && !is_string($id)) {
            throw new JsonApiException('Could not set the id of the error: value should be a number or a string');
        }

        $this->id = $id;
    }

    /**
     * Gets the id of this error
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the HTTP status code of this error
     * @param string $code
     * @return null
     */
    public function setStatusCode($statusCode) {
        if ($statusCode !== null && (!is_numeric($statusCode) || $statusCode < 400 || $statusCode > 599)) {
            throw new JsonApiException('Could not set the status code of the error: value should be an integer between 400 (4XX client error) and 599 (5XX server error)');
        }

        $this->statusCode = $statusCode;
    }

    /**
     * Gets the HTTP status code of this error
     * @return string
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Sets the code of this error
     * @param string $code An application-specific error code
     * @return null
     */
    public function setCode($code) {
        if ($code !== null && !is_numeric($code) && !is_string($code)) {
            throw new JsonApiException('Could not set the code of the error: value should be a number or a string');
        }

        $this->code = $code;
    }

    /**
     * Gets the code of this error
     * @return string An application-specific error code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Sets the title of this error
     * @param string $title A short, human-readable summary of the problem
     * @return null
     */
    public function setTitle($title) {
        if ($title !== null && !is_string($title)) {
            throw new JsonApiException('Could not set the title of the error: value should be a string');
        }

        $this->title = $title;
    }

    /**
     * Gets the title of this error
     * @return string A short, human-readable summary of the problem
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the detail of this error
     * @param string $detail a human-readable explanation specific to this
     * occurrence of the problem
     * @return null
     */
    public function setDetail($detail) {
        if ($detail !== null && !is_string($detail)) {
            throw new JsonApiException('Could not set the detail of the error: value should be a string');
        }

        $this->detail = $detail;
    }

    /**
     * Gets the detail of this error
     * @return string a human-readable explanation specific to this occurrence
     * of the problem
     */
    public function getDetail() {
        return $this->detail;
    }

    /**
     * Sets the source pointer of a resource attribute or relationship which
     * causes this error
     * @param string $sourcePointer a JSON pointer to the associated entity
     * @return null
     */
    public function setSourcePointer($sourcePointer) {
        if ($sourcePointer !== null && !is_string($sourcePointer)) {
            throw new JsonApiException('Could not set the source pointer of the error: value should be a string');
        }

        $this->sourcePointer = $sourcePointer;
    }

    /**
     * Gets the source pointer of a resource attribute or relationship which
     * causes this error
     * @return string a JSON pointer to the associated entity
     */
    public function getSourcePointer() {
        return $this->sourcePointer;
    }

    /**
     * Sets the query parameter which causes this error
     * @param string $sourceParameter string indicating which URI query
     * parameter caused the error
     * @return null
     */
    public function setSourceParameter($sourceParameter) {
        if ($sourceParameter !== null && !is_string($sourceParameter)) {
            throw new JsonApiException('Could not set the source parameter of the error: value should be a string');
        }

        $this->sourceParameter = $sourceParameter;
    }

    /**
     * Gets the source pointer of a resource attribute or relationship which
     * causes this error
     * @return string a string indicating which URI query parameter caused the
     * error
     */
    public function getSourceParameter() {
        return $this->sourceParameter;
    }

    /**
     * Specifies the data which should be serialized to JSON
     * @return array
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(): array {
        if ($this->id === null &&
            $this->statusCode === null &&
            $this->code === null &&
            $this->title === null &&
            $this->detail === null &&
            $this->sourcePointer === null &&
            $this->sourceParameter === null &&
            !$this->links &&
            !$this->meta
        ) {
            throw new JsonApiException('Could not serialize the error: no properties set to this error, set at least one property using a setter');
        }

        $value = array();

        if ($this->id) {
            $value['id'] = $this->id;
        }

        if ($this->links) {
            $value['links'] = $this->links;
        }

        if ($this->statusCode) {
            $value['status'] = $this->statusCode;
        }

        if ($this->code) {
            $value['code'] = $this->code;
        }

        if ($this->title) {
            $value['title'] = $this->title;
        }

        if ($this->detail) {
            $value['detail'] = $this->detail;
        }

        if ($this->sourcePointer || $this->sourceParameter) {
            $value['source'] = array();
            if ($this->sourcePointer) {
                $value['source']['pointer'] = $this->sourcePointer;
            }

            if ($this->sourceParameter) {
                $value['source']['parameter'] = $this->sourceParameter;
            }
        }

        if ($this->meta) {
            $value['meta'] = $this->meta;
        }

        return $value;
    }

}
