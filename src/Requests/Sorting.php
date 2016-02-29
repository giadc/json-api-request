<?php
namespace Giadc\JsonApiRequest\Requests;

class Sorting
{
    private $container = array();

    public function __construct($sort)
    {
        $this->setSorting($sort);
    }

    public function setSorting($sort)
    {
        if (empty($sort) && !is_string($sort))
            return;

        $fields = \explode(',', $sort);

        if (!empty($fields))
            $this->processFields($fields);        
    }

    private function processFields($fields)
    {
        foreach ($fields as &$field) {
            $direction = 'ASC';

            if ('-' === $field[0]) {
                $field = \ltrim($field, '-');
                $direction = 'DESC';
            }

            $this->container[] = ['field' => $field, 'direction' => $direction];
        }
    }

    public function toArray()
    {
        if (is_null($this->container))
            return [];

        return $this->container;
    }

    public function getParamsArray()
    {
        if ($this->container == null || (count($this->container) == 1 && $this->container[0] == null))
            return [];

        $sortParams = $this->toString();

        return [
            'sort' => $sortParams,
        ];
    }

    public function getQueryString()
    {
        if (count(array_filter($this->container)) == 0)
            return '';

        return 'sort=' . $this->toString();
    }

    private function toString()
    {
        $temp = '';
        $fields = $this->container;

        foreach ($fields as $key => &$field) {
            if ($field['direction'] === 'DESC')
                $temp.= '-';

            $temp .= $field['field'];

            if (! count($fields) == $key + 1 && count($fields) > 1)
                $temp .= ',';
        }

        return $temp;
    }
}
