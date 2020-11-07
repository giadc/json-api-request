<?php

namespace Giadc\JsonApiRequest\Requests;

abstract class AbstractArrayParams implements RequestInterface
{
    protected array $container = [];

    public function __construct(?array $items = [])
    {
        foreach ($items as $key => $item) {
            $this->add($key, $item);
        }
    }

    abstract protected static function paramKey(): string;

    /**
     * Add an additional item.
     *
     * @param string|array $value
     */
    public function add(string $key, $value): void
    {
        if (is_string($value)) {
            $value = trim($value) == '' ? [] : explode(',', $value);
        }

        $this->container[$key] = array_filter($value, function ($item) {
            return !(is_string($item) && trim($item) == '');
        });
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->container);
    }

    public function get(string $key): ?array
    {
        return $this->has($key) ? $this->container[$key] : null;
    }

    public function isEmpty(): bool
    {
        return empty($this->container);
    }


    /**
     * Convert Items to a string.
     */
    public function toString(): string
    {
        $params = '';
        $paramKey = $this->paramKey();

        foreach ($this->container as $key => $item) {
            if (!$params == '') {
                $params .= '&';
            }

            $params .= $paramKey . "[$key]=" . implode(',', $item);
        }

        return $params;
    }

    /**
     * Get Items as a params array.
     */
    public function getParamsArray(): array
    {
        return [$this->toString()];
    }

    /**
     * Get Items as query string fragment.
     */
    public function getQueryString(): string
    {
        return $this->toString();
    }

    /**
     * Get Items as a multi-dimensional array.
     */
    public function toArray(): array
    {
        if (is_null($this->container)) {
            return [];
        }

        return $this->container;
    }
}
