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

    /**
     * @phpstan-param array{number:int, size: int} $page
     */
    public static function fromArray(array $page): self
    {
        $number = isset($page['number']) ? $page['number'] : 1;
        $size   = isset($page['size']) ? $page['size'] : null;

        return new self($number, $size);
    }

    public static function fromRequest(Request $request): self
    {
        $page   = $request->query->all('page');
        $number = isset($page['number']) ? $page['number'] : 1;
        $size   = isset($page['size']) ? $page['size'] : null;

        return new self($number, $size);
    }

    /**
     * Get the current page number
     */
    public function getPageNumber(): int
    {
        return $this->number ?: 1;
    }

    /**
     * Get page offset (starting at zero)
     */
    public function getOffset(): int
    {
        return $this->getPageNumber() - 1;
    }

    /**
     * Get the page size
     */
    public function getPageSize(): int
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
     */
    public function getParamsArray(int $pageNumber = null): array
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
     */
    public function getQueryString(int $number = null): string
    {
        return sprintf(
            'page[number]=%d&page[size]=%d',
            $number ?: $this->getPageNumber(),
            $this->getPageSize()
        );
    }

    /**
     * Get the default page size
     */
    private function getDefaultSize(): int
    {
        return getenv('PAGINATION_DEFAULT') ?: 15;
    }

    /**
     * Get the maximum page size
     */
    private function getMaxSize(): int
    {
        return getenv('PAGINATION_MAX') ?: 25;
    }
}
