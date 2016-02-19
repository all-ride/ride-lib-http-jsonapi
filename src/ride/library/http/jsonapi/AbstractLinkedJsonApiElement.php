<?php

namespace ride\library\http\jsonapi;

use ride\library\http\jsonapi\exception\JsonApiException;

/**
 * Abstract implementation of an element with links for the JSON API
 */
abstract class AbstractLinkedJsonApiElement extends AbstractJsonApiElement {

    /**
     * Links of the element
     * @var array
     */
    protected $links = array();

    /**
     * Sets a link to this resource
     * @param string $name Name of the link (self, related, ...)
     * @param string|JsonApiLink $link Link URL or link object
     * @return JsonApiLink
     */
    public function setLink($name, $link) {
        if (is_string($link) || (is_object($link) && method_exists($link, '__toString'))) {
            $link = new JsonApiLink((string) $link);
        } elseif (!$link instanceof JsonApiLink) {
            throw new JsonApiException('Could not set link to this element: provided link should be a string or a JsonApiLink object');
        }

        $this->links[$name] = $link;

        return $link;
    }

    /**
     * Gets a link of this resource
     * @param string $name Name of the link (self, related, ...)
     * @return JsonApiLink|null
     */
    public function getLink($name) {
        if (!isset($this->links[$name])) {
            return null;
        }

        return $this->links[$name];
    }

    /**
     * Gets the links of this element
     * @return array Array with the name of the link as key and a link instance
     * as value
     * @see JsonApiLink
     */
    public function getLinks() {
        return $this->links;
    }

    /**
     * Removes all the links
     * @return null
     */
    public function clearLinks() {
        $this->links = array();
    }

}
