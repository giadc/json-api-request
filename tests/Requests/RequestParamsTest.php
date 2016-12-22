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
        $request = Request::create('http://test.com/articles'
            . '?include=author,comments.author'
            . '&page[number]=3&page[size]=20'
            . '&filter[author]=frank'
            . '&sort=-created,title'
        );

        $this->requestParams = new RequestParams($request);
    }

    public function test_it_returns_the_includes()
    {
        $expected = ['author', 'comments.author'];
        $includes = $this->requestParams->getIncludes();

        $this->assertInstanceOf(Includes::class, $includes);
        $this->assertEquals($expected, $includes->toArray());
    }

    public function test_it_returns_the_pagination()
    {
        $pagination = $this->requestParams->getPageDetails();

        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertEquals(3, $pagination->getPageNumber());
        $this->assertEquals(20, $pagination->getPageSize());
    }

    public function test_it_returns_the_sorting()
    {
        $expected = [
            ['field' => 'created', 'direction' => 'DESC'],
            ['field' => 'title', 'direction' => 'ASC'],
        ];

        $sorting = $this->requestParams->getSortDetails();

        $this->assertInstanceOf(Sorting::class, $sorting);
        $this->assertEquals($expected, $sorting->toArray());
    }

    public function test_it_returns_the_filters()
    {
        $expected = ['author' => ['frank']];
        $filters  = $this->requestParams->getFiltersDetails();

        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertEquals($expected, $filters->toArray());
    }
}
