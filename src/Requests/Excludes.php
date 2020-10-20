<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Excludes implements RequestInterface
{
    private array $container = [];

    public function __construct(array $excludes = [])
    {
        foreach ($excludes as $key => $exclude) {
            $this->add($key, $exclude);
        }
    }

    public static function fromRequest(Request $request): self
    {
        $excludes = $request->query->get('excludes');
        return new self($excludes ?: []);
    }

    /**
     * Add an additional exclude
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
     * Convert Excludes to a string
     *
     * @return string
     */
    public function toString(): string
    {
        $params = '';

        foreach ($this->container as $key => $exclude) {
            if (!$params == '') {
                $params .= '&';
            }

            $params .= "excludes[$key]=" . implode(',', $exclude);
        };

        return $params;
    }

    /**
     * Get Excludes as a params array
     *
     * @return array[string]
     */
    public function getParamsArray(): array
    {
        return [$this->toString()];
    }

    /**
     * Get Excludes as query string fragment
     */
    public function getQueryString(): string
    {
        return $this->toString();
    }

    /**
     * Get Excludes as a multi-dimensional array
     */
    public function toArray(): array
    {
        return $this->container;
    }
}
