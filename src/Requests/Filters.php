<?php
namespace Giadc\JsonApiRequest\Requests;

class Filters implements RequestInterface
{
    private $container = array();

    public function __construct($filters = [])
    {
        $filters = $filters ?: [];

        foreach ($filters as $key => $filter) {
            $this->addFilter($key, $filter);
        }
    }

    /**
     * Add an additional filter
     *
     * @param string       $key
     * @param string|array $value
     */
    public function addFilter($key, $value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        $this->container[$key] = $value;
    }

    /**
     * Convert Filters to a string
     *
     * @return string
     */
    public function toString()
    {
        $params = '';

        foreach ($this->container as $key => $filter) {
            if (!$params == '') {
                $params .= '&';
            }

            $params .= "filter[$key]=" . implode(',', $filter);
        };

        return $params;
    }

    /**
     * Get Filters as a params array
     *
     * @return array
     */
    public function getParamsArray()
    {
        return [$this->toString()];
    }

    /**
     * Get Filters as query string fragment
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->toString();
    }

    /**
     * Get Filters as a multi-dimensional array
     *
     * @return array
     */
    public function toArray()
    {
        if (is_null($this->container)) {
            return [];
        }

        return $this->container;
    }
}
