<?php
namespace Giadc\JsonApiRequest\Requests;

class Filters
{
    private $container = array();

    public function __construct($filters = [])
    {
        $filters = $filters ?: [];

        foreach ($filters as $key => $filter) {
            $this->container[$key] = explode(',', $filter);
        }
    }

    public function toString()
    {
        $params = '';

        foreach ($this->container as $key => $filter) {
            if( ! $params == '' )
                $params .= '&';

            $params .= "filter[$key]=" . implode(',', $filter);
        };

        return $params;
    }

    public function getParamsArray()
    {
        return [$this->toString()];
    }

    public function getQueryString()
    {
        return $this->toString();
    }

    public function toArray()
    {
        if (is_null($this->container))
            return [];

        return $this->container;
    }
}
