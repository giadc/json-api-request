<?php

use Giadc\JsonApiRequest\Requests\Sorting;
use PHPUnit\Framework\TestCase;

class SortingTest extends TestCase
{
    private $sorting;

    public function setUp(): void
    {
        $this->sorting = new Sorting('width,-height');
    }

    public function test_it_returns_an_array_for_fields_to_sort_by()
    {
        $expected = [
            ['field' => 'width', 'direction' => 'ASC'],
            ['field' => 'height', 'direction' => 'DESC'],
        ];
        $this->assertEquals($expected, $this->sorting->toArray());
    }

    public function test_it_returns_a_params_array()
    {
        $expected = [
            'sort' => 'width,-height',
        ];

        $this->assertEquals($expected, $this->sorting->getParamsArray());
    }

    public function test_it_returns_a_query_string()
    {
        $expected = 'sort=width,-height';
        $this->assertEquals($expected, $this->sorting->getQueryString());
    }

    public function test_it_replaces_sorting()
    {
        $expected = 'sort=weight,volume';
        $this->sorting->setSorting('weight,volume');
        $this->assertEquals($expected, $this->sorting->getQueryString());
    }
}
