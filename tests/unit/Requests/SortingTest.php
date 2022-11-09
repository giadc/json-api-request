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

    public function test_it_returns_an_array_for_fields_to_sort_by(): void
    {
        $expected = [
            ['field' => 'width', 'direction' => 'ASC'],
            ['field' => 'height', 'direction' => 'DESC'],
        ];
        $this->assertEquals($expected, $this->sorting->toArray());
    }

    public function test_it_returns_a_params_array(): void
    {
        $expected = [
            'sort' => 'width,-height',
        ];

        $this->assertEquals($expected, $this->sorting->getParamsArray());
    }

    public function test_it_returns_a_query_string(): void
    {
        $expected = 'sort=width,-height';
        $this->assertEquals($expected, $this->sorting->getQueryString());
    }

    public function test_it_replaces_sorting(): void
    {
        $expected = 'sort=weight,volume';
        $this->sorting->setWithString('weight,volume');
        $this->assertEquals($expected, $this->sorting->getQueryString());
    }

    public function test_it_can_add_sorting_field(): void
    {
        $expected = [
            ['field' => 'width', 'direction' => 'ASC'],
            ['field' => 'name', 'direction' => 'DESC'],
            ['field' => 'height', 'direction' => 'ASC'],
        ];

        $this->sorting->add('name', 'desc');
        $this->sorting->add('height', 'ASC');

        $this->assertEqualsCanonicalizing($expected, $this->sorting->toArray());
    }

    public function test_it_wont_error_when_provided_an_empty_string(): void
    {
        $sorting = new Sorting('');
        $this->assertEquals($sorting->getQueryString(), '');


        $sorting->setWithString('');
        $this->assertEquals($sorting->getQueryString(), '');
    }
}
