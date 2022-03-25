<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Includes implements RequestInterface
{
    private $container = array();

    public function __construct(array|string|null $includes = [])
    {
        if (!is_array($includes)) {
            $includes = is_string($includes) ? explode(',', $includes) : [];
        }

        $this->container = $includes;
    }

    public static function fromRequest(Request $request): self
    {
        $includes = $request->query->get('include');
        return new self($includes);
    }

    /**
     * Add an additional include
     */
    public function add(string|array $includes): void
    {
        if (!is_array($includes)) {
            $includes = [$includes];
        }

        $this->container = array_merge($this->container, $includes);
    }

    /**
     * Get Includes as an array
     */
    public function toArray(): array
    {
        if (is_null($this->container)) {
            return [];
        }

        return array_filter($this->container);
    }

    /**
     * Get Includes as a param array
     */
    public function getParamsArray(): array
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
     */
    public function getQueryString(): string
    {
        if (count(array_filter($this->container)) == 0) {
            return '';
        }

        return 'include=' . $this->toString();
    }

    /**
     * Get Includes as a comma-separated list
     */
    public function toString(): string
    {
        return implode(',', $this->container);
    }
}
