<?php

namespace ride\library\http\jsonapi;

/**
 * Data container for a JSON API resource
 */
class JsonApiResource extends AbstractLinkedJsonApiElement {

    /**
     * Type of the resource
     * @var string
     */
    protected $type;

    /**
     * Id of the resource
     * @var string
     */
    protected $id;

    /**
     * Attributes of the resource
     * @var array
     */
    protected $attributes;

    /**
     * Relationships of the resource
     * @var array
     */
    protected $relationships;

    /**
     * Constructs a new resource
     * @param string $type Type of the resource
     * @param string $id Id of the resource
     * @return null
     */
    public function __construct($type, $id = null) {
        $this->type = $type;
        $this->id = $id;
        $this->relationshipPath = null;
        $this->attributes = array();
        $this->relationships = array();
    }

    /**
     * Gets the type of this resource
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the id of this resource
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the relationship path of this resource
     * @param string $relationshipPath dot-separated list of the relationship
     * name
     * @return null
     */
    public function setRelationshipPath($relationshipPath = null) {
        $this->relationshipPath = $relationshipPath;
    }

    /**
     * Gets the relationship path of this resource
     * @return string|null dot-separated list of the relationship name
     */
    public function getRelationshipPath() {
        return $this->relationshipPath;
    }

    /**
     * Sets an attribute of this resource
     * @param string $name Name of the attribute
     * @param mixed $value Value for the attribute
     * @return null
     */
    public function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    /**
     * Gets an attribute of this resource
     * @param string $name Name of the attribute
     * @param mixed $default Default value for the attribute
     * @return mixed
     */
    public function getAttribute($name, $default = null) {
        if (!isset($this->attributes[$name])) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * Gets the attributes of this resource
     * @return array Array witht eh field name as key and the attribute as value
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Sets a relationship of this resource
     * @param string $name Name of the relationship
     * @param JsonApiRelationship $relationship Value of the relationship
     * @return null
     */
    public function setRelationship($name, JsonApiRelationship $relationship) {
        $this->relationships[$name] = $relationship;
    }

    /**
     * Gets a relationship of this resource
     * @param string $name Name of the relationship
     * @return JsonApiRelationship|null
     */
    public function getRelationship($name) {
        if (!isset($this->relationships[$name])) {
            return null;
        }

        return $this->relationships[$name];
    }

    /**
     * Gets the relationships of this resource
     * @return array Array with a relation instance as value
     * @see JsonApiRelationship
     */
    public function getRelationships() {
        return $this->relationships;
    }

    /**
     * Gets a representation of this element ready for JSON encoding
     * @param boolean $full Flag to see if the full version should be returned
     * @return scalar
     */
    public function getJsonValue($full = true) {
        $value = array(
            'type' => $this->type,
            'id' => $this->id,
        );

        if (!$full) {
            return $value;
        }

        if ($this->links) {
            $value['links'] = $this->links;
        }

        if ($this->attributes) {
            $value['attributes'] = $this->attributes;
        }

        if ($this->relationships) {
            $value['relationships'] = $this->relationships;
        }

        if ($this->meta) {
            $value['meta'] = $this->meta;
        }

        return $value;
    }

}
