<?php

namespace ride\library\http\jsonapi;

/**
 * Abstract implementation of an element for the JSON API
 */
abstract class AbstractJsonApiElement {

    /**
     * Meta of the element
     * @var array
     */
    protected $meta = array();

    /**
     * Sets the meta values or a single meta value
     * @param string|array $meta Name of the meta or an array with meta data
     * @param mixed $value Value for the meta, used when a name is provided
     * @return null
     */
    public function setMeta($meta, $value = null) {
        if (is_array($meta)) {
            $this->meta = $meta;
        } else {
            $this->meta[$meta] = $value;
        }
    }

    /**
     * Gets the meta of this element
     * @return array Array with the name of the meta as key and the meta as
     * value
     */
    public function getMeta() {
        return $this->meta;
    }

}
