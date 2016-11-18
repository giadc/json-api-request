<?php
namespace Giadc\JsonApiRequest\Requests;

class Includes
{
    private $container = array();

    public function __construct($includes = [])
    {
        if (!is_array($includes)) {
            $includes = explode(',', $includes);
        }

        $this->container = $includes;
    }

    /**
     * Add an additional include
     *
     * @param string|array $includes
     */
    public function add($includes)
    {
        if (!is_array($includes)) {
            $includes = [$includes];
        }

        $this->container = array_merge($this->container, $includes);
    }

    /**
     * Get Includes as an array
     *
     * @return array
     */
    public function toArray()
    {
        if (is_null($this->container)) {
            return [];
        }

        return array_filter($this->container);
    }

    /**
     * Get Includes as a param array
     *
     * @return array
     */
    public function getParamsArray()
    {
        if ($this->container == null || (count($this->container) == 1 && $this->container[0] == null)) {
            return [];
        }

        return [
            'include' => $this->toString(),
        ];
    }

    /**
     * Get Includes as a query string fragment
     *
     * @return string
     */
    public function getQueryString()
    {
        if (count(array_filter($this->container)) == 0) {
            return '';
        }

        return 'include=' . $this->toString();
    }

    /**
     * Get Includes as a comma-separated list
     *
     * @return string
     */
    public function toString()
    {
        return implode($this->container, ',');
    }
}
