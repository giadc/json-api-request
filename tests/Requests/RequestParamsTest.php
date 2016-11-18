<?php
use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiRequest\Requests\Sorting;
use Mockery as m;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RequestParamsTest extends PHPUnit_Framework_TestCase
{
    private $requestParams;

    public function setUp()
    {
        $query = m::mock(ParameterBag::class)
            ->shouldReceive('get')->with('include')->andReturn('one,two')
            ->shouldReceive('get')->with('page')->andReturn(['number' => 1, 'size' => 20])
            ->shouldReceive('get')->with('sort')->andReturn('width,-height')
            ->shouldReceive('get')->with('filter')->andReturn(['banana' => 'hammock'])
            ->getMock();

        $request = m::mock(Request::class);

        $request->query = $query;

        $this->requestParams = new RequestParams($request);
    }

    public function test_it_returns_the_includes()
    {
        $expected = ['one', 'two'];
        $includes = $this->requestParams->getIncludes();

        $this->assertInstanceOf(Includes::class, $includes);
        $this->assertEquals($expected, $includes->toArray());
    }

    public function test_it_returns_the_pagination()
    {
        $expected   = ['one', 'two'];
        $pagination = $this->requestParams->getPageDetails();

        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertEquals(1, $pagination->getPageNumber());
        $this->assertEquals(20, $pagination->getPageSize());
    }

    public function test_it_returns_the_sorting()
    {
        $expected = [
            ['field' => 'width', 'direction' => 'ASC'],
            ['field' => 'height', 'direction' => 'DESC'],
        ];

        $sorting = $this->requestParams->getSortDetails();

        $this->assertInstanceOf(Sorting::class, $sorting);
        $this->assertEquals($expected, $sorting->toArray());
    }

    public function test_it_returns_the_filters()
    {
        $expected = ['banana' => ['hammock']];
        $filters  = $this->requestParams->getFiltersDetails();

        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertEquals($expected, $filters->toArray());
    }
}
