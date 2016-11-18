<?php
namespace Giadc\JsonApiRequest\Requests;

class Sorting
{
    private $container = array();

    public function __construct($sorting)
    {
        $this->setSorting($sorting);
    }

    /**
     * Set the sorting
     *
     * @param string $sorting
     */
    public function setSorting($sorting)
    {
        $this->container = [];

        if (empty($sorting) && !is_string($sorting)) {
            return;
        }

        $fields = \explode(',', $sorting);

        if (!empty($fields)) {
            $this->processFields($fields);
        }
    }

    /**
     * Process an individual field
     *
     * @param  array $fields
     */
    private function processFields($fields)
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

    /**
     * Get Sorting as a params array
     *
     * @return array
     */
    public function getParamsArray()
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
     *
     * @return string
     */
    public function getQueryString()
    {
        if (count(array_filter($this->container)) == 0) {
            return '';
        }

        return 'sort=' . $this->toString();
    }

    /**
     * Get Sorting as a comma-separated list
     *
     * @return string
     */
    private function toString()
    {
        $fields = array_map(function ($field) {
            $prefix = ($field['direction'] === 'DESC') ? '-' : '';
            return $prefix . $field['field'];
        }, $this->container);

        return implode(',', $fields);
    }
}
