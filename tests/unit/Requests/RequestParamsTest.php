<?php

use Giadc\JsonApiRequest\Requests\Excludes;
use Giadc\JsonApiRequest\Requests\Fields;
use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\RequestParams;
use Giadc\JsonApiRequest\Requests\Sorting;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class RequestParamsTest extends TestCase
{
    private $requestParams;

    public function setUp(): void
    {
        $request = Request::create(
            'http://test.com/articles'
                . '?include=author,comments.author'
                . '&page[number]=3&page[size]=20'
                . '&filter[author]=frank'
                . '&sort=-created,title'
                . '&fields[author]=name'
                . '&excludes[articles]=body,updatedAt'
        );

        $this->requestParams = RequestParams::fromRequest($request);
    }

    public function test_it_returns_the_includes(): void
    {
        $expected = ['author', 'comments.author'];
        $includes = $this->requestParams->getIncludes();

        $this->assertInstanceOf(Includes::class, $includes);
        $this->assertEquals($expected, $includes->toArray());
    }

    public function test_it_returns_the_pagination(): void
    {
        $pagination = $this->requestParams->getPageDetails();

        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertEquals(3, $pagination->getPageNumber());
        $this->assertEquals(20, $pagination->getPageSize());
    }

    public function test_it_returns_the_sorting(): void
    {
        $expected = [
            ['field' => 'created', 'direction' => 'DESC'],
            ['field' => 'title', 'direction' => 'ASC'],
        ];

        $sorting = $this->requestParams->getSortDetails();

        $this->assertInstanceOf(Sorting::class, $sorting);
        $this->assertEquals($expected, $sorting->toArray());
    }

    public function test_it_returns_the_filters(): void
    {
        $expected = ['author' => ['frank']];
        $filters  = $this->requestParams->getFiltersDetails();

        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertEquals($expected, $filters->toArray());
    }

    public function test_it_returns_the_fields(): void
    {
        $expected = ['author' => ['name']];
        $fields  = $this->requestParams->getFields();

        $this->assertInstanceOf(Fields::class, $fields);
        $this->assertEquals($expected, $fields->toArray());
    }

    public function test_it_returns_the_excludes(): void
    {
        $expected = ['articles' => ['body', 'updatedAt']];
        $excludes  = $this->requestParams->getExcludes();

        $this->assertInstanceOf(Excludes::class, $excludes);
        $this->assertEquals($expected, $excludes->toArray());
    }

    public function test_it_can_create_and_modify_from_empty_request(): void
    {
        $params = RequestParams::fromEmptyRequest();

        $params->getFields()->add('campaigns', ['name', 'updatedBy', 'advertiser']);

        $this->assertInstanceOf(RequestParams::class, $params);
        $this->assertEquals(['name', 'updatedBy', 'advertiser'], $params->getFields()->get('campaigns'));
    }

    public function test_it_build_from_array(): void
    {
        $params = RequestParams::fromArray([
            'filters' => [
                'author' => 'frank',
            ],
            'pagination' => [
                'number' => 3,
                'size' => 20,
            ],
            'includes' => ['author'],
            'excludes' => [
                'campaigns' => ['name'],
                'articles' => ['body', 'updatedAt'],
            ],
            'sorting' => '-name',
        ]);


        $this->assertInstanceOf(RequestParams::class, $params);
        $this->assertEquals(
            'page[number]=3&page[size]=20,include=author,sort=-name,filter[author]=frank,excludes[campaigns]=name&excludes[articles]=body,updatedAt',
            $params->getQueryString()
        );
    }
}
