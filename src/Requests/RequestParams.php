<?php
namespace Giadc\JsonApi\Requests;

use Giadc\JsonApi\Requests\Sorting;
use Giadc\JsonApi\Requests\Includes;
use Giadc\JsonApi\Requests\Pagination;
use Giadc\JsonApi\Requests\Filters;
use Symfony\Component\HttpFoundation\Request;

class RequestParams
{
    private $request;

    function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function getIncludes()
    {
        $includes = $this->request->query->get('include');

        return new Includes($includes);
    }

    public function getPageDetails()
    {
        $page = $this->request->query->get('page');
        $number = isset($page['number']) ? $page['number'] : 1;
        $size = isset($page['size']) ? $page['size'] : null;

        return new Pagination($number, $size);
    }

    public function getSortDetails()
    {
        $sort = $this->request->query->get('sort');

        return new Sorting($sort);
    }

    public function getFiltersDetails()
    {
        $filters = $this->request->query->get('filter');

        return new Filters($filters);
    }

    public function getUri()
    {
        return explode('?', $this->request->getUri())[0];
    }

    public function getQueryString($pageNumber = null)
    {
        $array = [
            $this->getPageDetails()->getQueryString($pageNumber),
            $this->getIncludes()->getQueryString(),
            $this->getSortDetails()->getQueryString(),
            $this->getFiltersDetails()->getQueryString(),
        ];

        return implode(array_filter($array), '&');
    }

    public function getFullPagination()
    {
        return [
            $this->getPageDetails(),
            $this->getIncludes(),
            $this->getSortDetails(),
            $this->getFiltersDetails(),
        ];
    }
}
