<?php

namespace ride\library\http\jsonapi;

use \JsonSerializable;
use ReturnTypeWillChange;

/**
 * Interface for a JSON API link
 */
class JsonApiLink extends AbstractJsonApiElement implements JsonSerializable {

    /**
     * URL of this resource link
     * @var string
     */
    protected $href;

    /**
     * Constructs a new resource link
     * @param string $href URL of the link
     * @return null
     */
    public function __construct($href) {
        $this->href = $href;
    }

    /**
     * Gets the URL of the link
     * @return string
     */
    public function getHref() {
        return $this->href;
    }

    /**
     * Specifies the data which should be serialized to JSON
     * @return string|array
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize() {
        if (!$this->meta) {
            return $this->href;
        }

        return array(
            'href' => $this->href,
            'meta' => $this->meta,
        );
    }

}
