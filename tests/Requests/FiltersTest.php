<?php

use Giadc\JsonApiRequest\Requests\Filters;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private $filters;

    public function setUp(): void
    {
        $this->filters = new Filters([
            'name' => 'Example name',
            'city' => 'Indianapolis,Des Moines',
        ]);
    }

    public function test_it_returns_a_filter_array()
    {
        $expected = [
            'name' => ['Example name'],
            'city' => ['Indianapolis', 'Des Moines'],
        ];

        $this->assertEquals($expected, $this->filters->toArray());
    }

    public function test_it_returns_a_query_string()
    {
        $expected = 'filter[name]=Example name&filter[city]=Indianapolis,Des Moines';
        $this->assertEquals($expected, $this->filters->getQueryString());
        $this->assertEquals([$expected], $this->filters->getParamsArray());
    }

    public function test_it_allows_an_additional_filter_to_be_added()
    {
        $expected = [
            'name'   => ['Example name'],
            'city'   => ['Indianapolis', 'Des Moines'],
            'gender' => ['Attack Helicopter'],
        ];

        $this->filters->addFilter('gender', 'Attack Helicopter');
        $this->assertEquals($expected, $this->filters->toArray());
    }
}
