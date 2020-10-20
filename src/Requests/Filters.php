<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Filters implements RequestInterface
{
    private $container = array();

    public function __construct($filters = [])
    {
        $filters = $filters ?: [];

        foreach ($filters as $key => $filter) {
            $this->add($key, $filter);
        }
    }

    public static function fromRequest(Request $request): self
    {
        $filters = $request->query->get('filter');
        return new self($filters);
    }

    /**
     * Add an additional filter
     *
     * @param string|array $value
     */
    public function add(string $key, $value): void
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        $this->container[$key] = $value;
    }

    /**
     * Convert Filters to a string
     */
    public function toString(): string
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
     */
    public function getParamsArray(): array
    {
        return [$this->toString()];
    }

    /**
     * Get Filters as query string fragment
     */
    public function getQueryString(): string
    {
        return $this->toString();
    }

    /**
     * Get Filters as a multi-dimensional array
     */
    public function toArray(): array
    {
        if (is_null($this->container)) {
            return [];
        }

        return $this->container;
    }
}
