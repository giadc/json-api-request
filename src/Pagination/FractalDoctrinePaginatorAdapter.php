<?php
namespace Giadc\JsonApi\Pagination;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Giadc\JsonApi\Requests\RequestParams;
use League\Fractal\Pagination\PaginatorInterface;

class FractalDoctrinePaginatorAdapter implements PaginatorInterface
{
    /**
     * The paginator instance.
     *
     * @var \Doctrine\ORM\Tools\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The route generator.
     *
     * @var callable
     */
    protected $routeGenerator;

    /**
     * The RequestParams instance.
     *
     * @var RequestParams
     */
    protected $requestParams;

    /**
     * Create a new doctrine pagination adapter.
     *
     * @param Paginator $paginator
     * @param RequestParams $requestParams
     *
     * @return void
     */
    public function __construct(Paginator $paginator, RequestParams $requestParams)
    {
        $this->paginator = $paginator;
        $this->request = $requestParams;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $paginator = $this->request->getPageDetails();
        return $paginator->getPageNumber();
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function getLastPage()
    {
        $paginator = $this->request->getPageDetails();
        $resultsPerPage = $paginator->getPageSize();

        return ceil($this->getTotal() / $resultsPerPage);
    }

    /**
     * Get the total.
     *
     * @return int
     */
    public function getTotal()
    {
        return count($this->paginator);
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
        if ($this->getPerPage() > $this->getTotal())
            return $this->getTotal();

        return $this->getPerPage();
    }

    /**
     * Get the number per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        $paginator = $this->request->getPageDetails();
        return $paginator->getPageSize();
    }

    /**
     * Get the url for the given page.
     *
     * @param int $page
     *
     * @return string
     */
    public function getUrl($page)
    {
        $url = $this->request->getUri();
        $params = $this->request->getQueryString($page);

        return $url . '?' . $params;
    }

    /**
     * Get the paginator instance.
     *
     * @return \Doctrine\ORMTools\Pagination\Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Get the the route generator.
     *
     * @return callable
     */
    public function getRouteGenerator()
    {
        return $this->routeGenerator;
    }
}
