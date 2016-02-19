<?php
namespace Giadc\JsonApi\Requests;

class Includes
{
    private $container = array();

    public function __construct($includes = [])
    {
        if (! is_array($includes)) {
            $includes = explode(',', $includes);
        }

        $this->container = $includes;
    }

    public function add($includes)
    {
        if (!is_array($includes)) {
            $includes = [$includes];
        }

        $this->container = array_merge($this->container, $includes);
    }

    public function toArray()
    {
        if (is_null($this->container))
            return [];

        return array_filter($this->container);
    }

    public function getParamsArray()
    {
        if ($this->container == null || (count($this->container) == 1 && $this->container[0] == null))
            return [];

        return [
            'include' => $this->toString(),
        ];
    }

    public function getQueryString()
    {
        if (count(array_filter($this->container)) == 0)
            return '';

        return 'include=' . $this->toString();
    }

    public function toString()
    {
         return implode($this->container, ',');
    }
}
