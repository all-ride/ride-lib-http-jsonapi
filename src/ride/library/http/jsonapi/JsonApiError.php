<?php

namespace ride\library\http\jsonapi;

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
     * @param string $sourcePointer a JSON pointer to the associated entity
     * @return null
     */
    public function setSourceParameter($sourceParameter) {
        $this->sourceParameter = $sourceParameter;
    }

    /**
     * Gets the source pointer of a resource attribute or relationship which
     * causes this error
     * @return string a JSON pointer to the associated entity
     */
    public function getSourceParameter() {
        return $this->sourceParameter;
    }

    /**
     * Specifies the data which should be serialized to JSON
     * @return array
     */
    public function jsonSerialize() {
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
