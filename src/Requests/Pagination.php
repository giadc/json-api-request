<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Pagination implements RequestInterface
{
    private int $number;

    private ?int $size;

    public function __construct(int $number = 1, int $size = null)
    {
        $this->number = $number;
        $this->size   = $size;
    }

    public static function fromRequest(Request $request): self
    {
        $page   = $request->query->get('page');
        $number = isset($page['number']) ? $page['number'] : 1;
        $size   = isset($page['size']) ? $page['size'] : null;

        return new self($number, $size);
    }

    /**
     * Get the current page number
     *
     * @return int
     */
    public function getPageNumber()
    {
        return $this->number ?: 1;
    }

    /**
     * Get page offset (starting at zero)
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->getPageNumber() - 1;
    }

    /**
     * Get the page size
     *
     * @return string
     */
    public function getPageSize()
    {
        if ($this->size == null) {
            return $this->getDefaultSize();
        }

        return ($this->size > $this->getMaxSize())
            ? $this->getMaxSize()
            : (int) $this->size;
    }

    /**
     * Get Pagination as a param array for the given page number
     *
     * @param  integer $pageNumber
     * @return array
     */
    public function getParamsArray($pageNumber = null)
    {
        return [
            'page' => array_filter(
                [
                    'number' => $pageNumber ?: $this->getPageNumber(),
                    'size'   => $this->getPageSize(),
                ]
            ),
        ];
    }

    /**
     * Get Pagination as a query string fragment for the given page
     *
     * @param  int $number
     * @return string
     */
    public function getQueryString($number = null)
    {
        $number = $number == null ? $this->getPageNumber() : $number;

        return sprintf(
            'page[number]=%d&page[size]=%d',
            $number,
            $this->getPageSize()
        );
    }

    /**
     * Get the default page size
     *
     * @return int
     */
    private function getDefaultSize()
    {
        return getenv('PAGINATION_DEFAULT') ?: 15;
    }

    /**
     * Get the maximum page size
     *
     * @return int
     */
    private function getMaxSize()
    {
        return getenv('PAGINATION_MAX') ?: 25;
    }
}
