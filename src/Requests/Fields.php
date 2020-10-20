<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Fields implements RequestInterface
{
    private array $container = [];

    public function __construct(?array $fields = [])
    {
        foreach ($fields as $key => $field) {
            $this->add($key, $field);
        }
    }

    public static function fromRequest(Request $request): self
    {
        $fields = $request->query->get('fields');
        return new self($fields ?? []);
    }

    /**
     * Add an additional field
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
     * Convert Fields to a string
     */
    public function toString(): string
    {
        $params = '';

        foreach ($this->container as $key => $field) {
            if (!$params == '') {
                $params .= '&';
            }

            $params .= "fields[$key]=" . implode(',', $field);
        };

        return $params;
    }

    /**
     * Get Fields as a params array
     */
    public function getParamsArray(): array
    {
        return [$this->toString()];
    }

    /**
     * Get Fields as query string fragment
     */
    public function getQueryString(): string
    {
        return $this->toString();
    }

    /**
     * Get Fields as a multi-dimensional array
     */
    public function toArray(): array
    {
        if (is_null($this->container)) {
            return [];
        }

        return $this->container;
    }
}
