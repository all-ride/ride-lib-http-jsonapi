<?php

namespace ride\library\http\jsonapi;

use ride\library\http\jsonapi\exception\BadRequestJsonApiException;

/**
 * Helper to parse the query parameters for your API endpoint
 */
class JsonApiQuery {

    /**
     * Parameter to include/exclude fields
     * @var string
     */
    const PARAMETER_FIELDS = 'fields';

    /**
     * Parameter to filter the result
     * @var string
     */
    const PARAMETER_FILTER = 'filter';

    /**
     * Parameter to include/exclude types
     * @var string
     */
    const PARAMETER_INCLUDE = 'include';

    /**
     * Parameter to limit the results
     * @var string
     */
    const PARAMETER_LIMIT = 'limit';

    /**
     * Parameter to start the results from a certain offset
     * @var string
     */
    const PARAMETER_OFFSET = 'offset';

    /**
     * Parameter to hold the limit and offset
     * @var string
     */
    const PARAMETER_PAGE = 'page';

    /**
     * Parameter to define the sort of the result
     * @var string
     */
    const PARAMETER_SORT = 'sort';

    /**
     * Ascending sort direction
     * @var string
     */
    const SORT_ASC = 'ASC';

    /**
     * Descending sort direction
     * @var string
     */
    const SORT_DESC = 'DESC';

    /**
     * Received query parameters
     * @var array
     */
    protected $parameters;

    /**
     * Parsed fields parameter
     * @var array
     */
    protected $fields;

    /**
     * Parsed include parameter
     * @var array
     */
    protected $include;

    /**
     * Constructs a new helper for the API query parameters
     * @param array $parameters Query parameters eg. $_GET
     * @return null
     */
    public function __construct(array $parameters) {
        $this->parameters = $parameters;
        $this->include = false;
        $this->fields = false;
    }

    /**
     * Gets the requested included resources
     * @return array|null Array with the resource type as key or null when no
     * includes are defined
     */
    public function getInclude() {
        if ($this->include !== false) {
            return $this->include;
        }

        $this->include = $this->getParameter(self::PARAMETER_INCLUDE);
        if ($this->include) {
            $this->include = $this->parseArrayParameter($this->include, array($this, 'parseArrayItemGeneric'));
        } else {
            $this->include = null;
        }

        return $this->include;
    }

    /**
     * Gets whether a relationship is requested
     * @param string $relationshipPath dot-separated list of relationship name
     * @return boolean
     */
    public function isIncluded($relationshipPath) {
        $include = $this->getInclude();

        if ($include === null) {
            if ($relationshipPath && strpos($relationshipPath, '.') === false) {
                return true;
            } else {
                return false;
            }
        }

        if ($relationshipPath === null) {
            return true;
        }

        foreach ($include as $included => $null) {
            if ($included == $relationshipPath || strpos($included, $relationshipPath . '.') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the requested included fields
     * @param string $type Type to get the fields for
     * @return array Array with the included field name as key
     * @throws \ride\library\http\jsonapi\exception\BadRequestJsonApiException
     * when the fields parameter is invalid
     */
    public function getFields($type) {
        if ($this->fields === false) {
            $this->fields = $this->getParameter(self::PARAMETER_FIELDS);
            if ($this->fields !== null) {
                if (!is_array($this->fields)) {
                    $exception = new BadRequestJsonApiException('Provided fields parameter should be an array with the resource type as key and a comma separated field list as value.');
                    $exception->setParameter(self::PARAMETER_FIELDS);

                    throw $exception;
                }

                foreach ($this->fields as $fieldType => $fieldFields) {
                    $this->fields[$fieldType] = $this->parseArrayParameter($fieldFields, array($this, 'parseArrayItemGeneric'));
                }
            } else {
                $this->fields = array();
            }
        }

        if (!isset($this->fields[$type])) {
            return null;
        }

        return $this->fields[$type];
    }

    /**
     * Gets whether a field is requested
     * @param string $type Type of the resource
     * @param string $fieldName Name of the field
     * @return boolean
     */
    public function isFieldRequested($type, $fieldName) {
        $fields = $this->getFields($type);

        if ($fields === null) {
            return true;
        }

        return isset($fields[$fieldName]);
    }

    /**
     * Gets all the filters
     * @return array Array with the name of the filter as key
     */
    public function getFilters() {
        return $this->getParameter(self::PARAMETER_FILTER, array());
    }

    /**
     * Gets a filter by name
     * @param string $name Name of the filter
     * @param mixed $default Default value
     * @return mixed
     */
    public function getFilter($name, $default = null) {
        return $this->getSubParameter(self::PARAMETER_FILTER, $name, $default);
    }

    /**
     * Gets the requested limit
     * @param integer $default Default value for the limit parameter
     * @param integer $maximum Maximum value for the limit parameter
     * @return integer
     */
    public function getLimit($default = 1000, $maximum = null) {
        $limit = $this->getSubParameter(self::PARAMETER_PAGE, self::PARAMETER_LIMIT, $default);

        if (!is_numeric($limit) || $limit < 1) {
            $exception = new BadRequestJsonApiException('Provided limit parameter should be an integer greater then or equals to 1.');
            $exception->setParameter(self::PARAMETER_PAGE);

            throw $exception;
        } elseif ($maximum && $limit > $maximum) {
            $exception = new BadRequestJsonApiException('Provided limit parameter cannot be greater than ' . $maximum . '.');
            $exception->setParameter(self::PARAMETER_PAGE);

            throw $exception;
        }

        return (integer) $limit;
    }

    /**
     * Gets the requested offset
     * @return integer
     */
    public function getOffset() {
        $offset = $this->getSubParameter(self::PARAMETER_PAGE, self::PARAMETER_OFFSET, 0);
        if (!is_numeric($offset) || $offset < 0) {
            $exception = new BadRequestJsonApiException('Provided offset parameter should be an integer greater then or equals to 0.');
            $exception->setParameter(self::PARAMETER_PAGE);

            throw $exception;
        }

        return (integer) $offset;
    }

    /**
     * Gets the sort
     * @param string $default Default value for the sort parameter
     * @return array Array with the field as key and the sort direction as value
     */
    public function getSort($default = null) {
        $sort = $this->getParameter(self::PARAMETER_SORT, $default);
        $sort = $this->parseArrayParameter($sort, array($this, 'parseArrayItemSort'));

        return $sort;
    }

    /**
     * Gets a query parameter
     * @param string $name Name of the parameter
     * @param mixed $default Value to return when the parameter is not set
     * @return mixed
     */
    public function getParameter($name, $default = null) {
        if (!isset($this->parameters[$name])) {
            return $default;
        }

        return $this->parameters[$name];
    }

    /**
     * Gets a sub query parameter
     * @param string $name Name of the parameter
     * @param string $subName Name of the sub parameter (next level)
     * @param mixed $default Value to return when the parameter is not set
     * @return mixed
     */
    protected function getSubParameter($name, $subName, $default = null) {
        if (!isset($this->parameters[$name][$subName])) {
            return $default;
        }

        return $this->parameters[$name][$subName];
    }

    /**
     * Parses a comma separated value to an array indexed on the values
     * @param string $list Comma separated list
     * @return array Array with the list value as key
     */
    protected function parseArrayParameter($list, $callback) {
        $result = array();

        if (!$list) {
            return $result;
        } elseif (!is_string($list)) {
            throw new BadRequestJsonApiException('Could not parse parameter: value should be a string');
        }

        $array = explode(',', $list);
        foreach ($array as $value) {
            list($key, $value) = call_user_func($callback, $value);

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Parses an array item for a generic context
     * @param string $value Value to parse
     * @return array Array with the value as first item and true as second
     */
    protected function parseArrayItemGeneric($value) {
        return array($value, true);
    }

    /**
     * Parses an array item for a sort context
     * @param string $value Value to parse
     * @return array Array with the field name as first element, and ASC or DESC
     * as second
     */
    protected function parseArrayItemSort($value) {
        $direction = substr($value, 0, 1);
        switch ($direction) {
            case '-':
                return array(substr($value, 1), self::SORT_DESC);
            case '+':
                $value = substr($value, 1);
            default:
                return array($value, self::SORT_ASC);
        }
    }

}
