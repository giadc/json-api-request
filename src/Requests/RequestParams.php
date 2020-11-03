<?php

namespace Giadc\JsonApiRequest\Requests;

use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\Sorting;
use Symfony\Component\HttpFoundation\Request;

class RequestParams
{
    private Request $request;

    private Excludes $excludes;

    private Fields $fields;

    private Includes $includes;

    private Pagination $pagination;

    private Sorting $sorting;

    private Filters $filters;

    public function __construct($request = null)
    {
        $this->request = (is_null($request))
            ? Request::createFromGlobals()
            : $request;

        $this->excludes = Excludes::fromRequest($this->request);
        $this->fields = Fields::fromRequest($this->request);
        $this->includes = Includes::fromRequest($this->request);
        $this->pagination = Pagination::fromRequest($this->request);
        $this->sorting = Sorting::fromRequest($this->request);
        $this->filters = Filters::fromRequest($this->request);
    }

    public static function fromEmptyRequest()
    {
        return new self(new Request());
    }

    public function getExcludes(): Excludes
    {
        return $this->excludes;
    }

    public function getFields(): Fields
    {
        return $this->fields;
    }

    public function getIncludes(): Includes
    {
        return $this->includes;
    }

    public function getPageDetails(): Pagination
    {
        return $this->pagination;
    }

    public function getSortDetails(): Sorting
    {
        return $this->sorting;
    }

    public function getFiltersDetails(): Filters
    {
        return $this->filters;
    }

    /**
     * Get the current URI
     */
    public function getUri(): ?string
    {
        $uri = explode('?', $this->request->getUri());
        return  is_array($uri) ? $uri[0] : null;
    }

    /**
     * Get combined query string
     */
    public function getQueryString(int $pageNumber = null): string
    {
        $array = [
            $this->getPageDetails()->getQueryString($pageNumber),
            $this->getIncludes()->getQueryString(),
            $this->getSortDetails()->getQueryString(),
            $this->getFiltersDetails()->getQueryString(),
            $this->getFields()->getQueryString(),
            $this->getExcludes()->getQueryString(),
        ];

        return implode(',', array_filter($array));
    }

    /**
     * Get Pagination, Includes, Sorting, and Filters
     *
     * @return array
     */
    public function getFullPagination(): array
    {
        return [
            $this->getPageDetails(),
            $this->getIncludes(),
            $this->getSortDetails(),
            $this->getFiltersDetails(),
        ];
    }
}
