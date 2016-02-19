<?php
namespace Giadc\JsonApi\Requests;

class Pagination
{
    private $number;

    private $size;

    public function __construct($number = 1, $size = null)
    {
        $this->number = $number;
        $this->size = $size;
    }

    public function getPageNumber()
    {
        return $this->number ?: 1;
    }

    public function getOffset()
    {
        return $this->getPageNumber() - 1;
    }

    public function getPageSize()
    {
        if ($this->size == null) {
            return $this->getDefaultSize();
        }

        if ($this->size > $this->getMaxSize()) {
            return $this->getMaxSize();
        }

        return (integer) $this->size;
    }

    public function getParamsArray($number = 1)
    {
        return [
            'page' => array_filter(
                [
                    'number' => $number ?: $this->getPageNumber(),
                    'size' => $this->getPageSize(),
                ]
            ),
        ];
    }

    public function getQueryString($number = null)
    {
        $number = $number == null ? $this->getPageNumber() : $number;

        return 'page[number]=' . $number
            . '&page[size]=' . $this->getPageSize();
    }

    private function getDefaultSize()
    {
        return getenv('PAGINATION_DEFAULT') ?: 15;
    }

    private function getMaxSize()
    {
        return getenv('PAGINATION_MAX') ?: 25;
    }

}
