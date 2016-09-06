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
     * Gets meta from this element
     * @param string $meta Name of the meta to fetch or null for all meta
     * @return array|mixed Array with all the meta when no $meta argument
     * provided or the value of the meta with $default as fallback
     */
    public function getMeta($meta = null, $default = null) {
        if ($meta === null) {
            return $this->meta;
        } elseif (!isset($this->meta[$meta])) {
            return $default;
        }

        return $this->meta[$meta];
    }

}
