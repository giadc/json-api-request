<?php
namespace Giadc\JsonApiRequest\Requests;

use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\Sorting;
use Symfony\Component\HttpFoundation\Request;

class RequestParams
{
    private $request;

    public function __construct(Request $request = null)
    {
        $this->request = (is_null($request))
            ? Request::createFromGlobals()
            : $request;
    }

    /**
     * Get Includes
     *
     * @return Includes
     */
    public function getIncludes()
    {
        $includes = $this->request->query->get('include');
        return new Includes($includes);
    }

    /**
     * Get Pagination
     *
     * @return Pagination
     */
    public function getPageDetails()
    {
        $page   = $this->request->query->get('page');
        $number = isset($page['number']) ? $page['number'] : 1;
        $size   = isset($page['size']) ? $page['size'] : null;

        return new Pagination($number, $size);
    }

    /**
     * Get Sorting
     *
     * @return Sorting
     */
    public function getSortDetails()
    {
        $sort = $this->request->query->get('sort');
        return new Sorting($sort);
    }

    /**
     * Get Filters
     *
     * @return Filters
     */
    public function getFiltersDetails()
    {
        $filters = $this->request->query->get('filter');
        return new Filters($filters);
    }

    /**
     * Get the current URI
     *
     * @return [type] [description]
     */
    public function getUri()
    {
        return explode('?', $this->request->getUri())[0];
    }

    /**
     * Get combined query string
     *
     * @param  int $pageNumber
     * @return string
     */
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

    /**
     * Get Pagination, Includes, Sorting, and Filters
     *
     * @return array
     */
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
