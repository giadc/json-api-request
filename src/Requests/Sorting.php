<?php

namespace Giadc\JsonApiRequest\Requests;

use Giadc\JsonApiRequest\Exceptions\InvalidSortDirectionsException;
use Symfony\Component\HttpFoundation\Request;

class Sorting implements RequestInterface
{
    const VALID_DIRECTIONS = ['ASC', 'DESC'];

    private array $container = [];

    public function __construct(?string $sorting)
    {
        if (is_string($sorting) && strlen($sorting) > 0) {
            $this->setWithString($sorting);
        }
    }

    public static function fromRequest(Request $request): self
    {
        $sort = $request->query->get('sort');
        return new self($sort);
    }

    public function add(string $field, string $direction = 'ASC'): void
    {
        if (!in_array(strtoupper($direction), self::VALID_DIRECTIONS)) {
            throw new InvalidSortDirectionsException($field, $direction);
        }

        // If Field already exists..remove it.
        $this->container = array_filter($this->container, function ($sortingField) use ($field) {
            return $sortingField['field'] !== $field;
        });

        $this->container[] = [
            'field' => $field,
            'direction' => strtoupper($direction)
        ];
    }

    /**
     * Set the sorting
     */
    public function setWithString(string $sorting): void
    {
        // reset Container
        $this->container = [];

        if (!is_string($sorting) || strlen($sorting) === 0) {
            return;
        }

        $fields = \explode(',', $sorting);

        if (!empty($fields)) {
            $this->processFields($fields);
        }
    }

    /**
     * Process an individual field
     */
    private function processFields(array $fields): void
    {
        foreach ($fields as &$field) {
            $direction = 'ASC';

            if ('-' === $field[0]) {
                $field     = \ltrim($field, '-');
                $direction = 'DESC';
            }

            $this->container[] = ['field' => $field, 'direction' => $direction];
        }
    }

    /**
     * Get Sorting as an array
     */
    public function toArray(): array
    {
        if (is_null($this->container)) {
            return [];
        }

        return $this->container;
    }

    /**
     * Get Sorting as a params array
     */
    public function getParamsArray(): array
    {
        if ($this->container == null || (count($this->container) == 1 && $this->container[0] == null)) {
            return [];
        }

        $sortingParams = $this->toString();

        return [
            'sort' => $sortingParams,
        ];
    }

    /**
     * Get Sorting as a query string fragment
     */
    public function getQueryString(): string
    {
        if (count(array_filter($this->container)) == 0) {
            return '';
        }

        return 'sort=' . $this->toString();
    }

    /**
     * Get Sorting as a comma-separated list
     */
    private function toString(): string
    {
        $fields = array_map(function ($field) {
            $prefix = ($field['direction'] === 'DESC') ? '-' : '';
            return $prefix . $field['field'];
        }, $this->container);

        return implode(',', $fields);
    }
}
