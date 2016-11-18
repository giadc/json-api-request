<?php

use Giadc\JsonApiRequest\Requests\Includes;

class IncludesTest extends PHPUnit_Framework_TestCase
{
    private $includes;

    public function setUp()
    {
        $this->includes = new Includes(['first', 'second']);
    }

    public function test_it_returns_an_array_of_includes()
    {
        $expected = ['first', 'second'];
        $this->assertEquals($expected, $this->includes->toArray());
    }

    public function test_it_returns_a_query_string()
    {
        $expected = 'include=first,second';
        $this->assertEquals($expected, $this->includes->getQueryString());
    }

    public function test_it_returns_a_params_array()
    {
        $expected = ['include' => 'first,second'];
        $this->assertEquals($expected, $this->includes->getParamsArray());
    }

    public function test_it_returns_a_comma_separated_list_of_includes()
    {
        $expected = 'first,second';
        $this->assertEquals($expected, $this->includes->toString());
    }
}
