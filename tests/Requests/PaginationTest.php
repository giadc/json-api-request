<?php

use Giadc\JsonApiRequest\Requests\Pagination;

class PaginationTest extends PHPUnit_Framework_TestCase
{
    private $pagination;

    public function setUp()
    {
        $this->pagination = new Pagination(2, 20);
    }

    public function test_it_returns_the_current_page_number()
    {
        $this->assertEquals(2, $this->pagination->getPageNumber());
    }

    public function test_it_returns_the_page_size()
    {
        $this->assertEquals(20, $this->pagination->getPageSize());
    }

    public function test_it_returns_the_page_offset()
    {
        $this->assertEquals(1, $this->pagination->getOffset());
    }

    public function test_it_returns_a_params_array()
    {
        $expected = [
            'page' => [
                'number' => 2,
                'size'   => 20,
            ],
        ];

        $this->assertEquals($expected, $this->pagination->getParamsArray());
    }

    public function test_it_returns_a_query_string()
    {
        $expected = 'page[number]=2&page[size]=20';
        $this->assertEquals($expected, $this->pagination->getQueryString());
    }

    public function test_it_defaults_page_size()
    {
        $pagination = new Pagination(2);
        $this->assertEquals(15, $pagination->getPageSize());
    }

    public function test_it_limits_page_size()
    {
        $pagination = new Pagination(2, 200);
        $this->assertEquals(25, $pagination->getPageSize());
    }
}
