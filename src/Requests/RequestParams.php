<?php

namespace Giadc\JsonApiRequest\Requests;

use Giadc\JsonApiRequest\Exceptions\InvalidRequestParamsProvidedException;
use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\Sorting;
use Symfony\Component\HttpFoundation\Request;

class RequestParams
{
    const VALID_PARAM_KEYS = [
        'filters',
        'excludes',
        'fields',
        'includes',
        'pagination',
        'sorting'
    ];

    const ALIASES = [
        'page' => 'pagination',
        'sort' => 'sorting',
        'include' => 'includes',
        'filter' => 'filters',
    ];

    private Request $request;

    private Excludes $excludes;

    private Fields $fields;

    private Includes $includes;

    private Pagination $pagination;

    private Sorting $sorting;

    private Filters $filters;

    public function __construct(mixed $input = null)
    {
        if (!is_array($input) && !$input instanceof Request && !is_null($input)) {
            throw new \InvalidArgumentException(sprintf(
                'RequestParams expects `$input` to be an instance of %s, or have type of null/array. %s given.',
                Request::class,
                is_object($input) ? get_class($input) : gettype($input)
            ));
        }

        is_array($input)
            ? $this->buildParamsFromArray($input)
            : $this->buildParamsFromRequest($input);
    }

    public static function fromArray(array $parameters): self
    {
        return new self($parameters);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request);
    }

    public static function fromEmptyRequest(): self
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

        return implode('&', array_filter($array));
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

    /**
     * Ensures no incorrect RequestParameter types have been provided.
     *
     * @phpstan-param array<string, mixed> $parameters
     */
    private function assertValidKeys(array $parameters): void
    {
        $invalidKeys = array_diff(array_keys($parameters), self::VALID_PARAM_KEYS);

        if (count($invalidKeys) > 0) {
            throw new InvalidRequestParamsProvidedException($invalidKeys);
        }
    }

    /**
     * Allows alternative names for Criteria pieces.
     *
     * `sort` instead of `sorting`
     * `include` instead of `includes`
     * `filter` instead of `filters`
     * `page` instead of `pagination`
     */
    private function translateAliases(array $parameters): array
    {
        return array_reduce(
            array_keys($parameters),
            function ($carrier, $key) use ($parameters) {
                $keyToUse = array_key_exists($key, self::ALIASES)
                    ? self::ALIASES[$key]
                    : $key;

                $value = $parameters[$key];
                $carrier[$keyToUse] = $value;

                return $carrier;
            },
            []
        );
    }

    private function buildParamsFromArray(array $parameters): void
    {
        $parameters = $this->translateAliases($parameters);
        $this->assertValidKeys($parameters);

        $this->request = new Request();
        $this->excludes = new Excludes($parameters['excludes'] ?? []);
        $this->fields = new Fields($parameters['fields'] ?? []);
        $this->includes = new Includes($parameters['includes'] ?? []);
        $this->pagination = Pagination::fromArray($parameters['pagination'] ?? []);
        $this->sorting = new Sorting($parameters['sorting'] ?? '');
        $this->filters = new Filters($parameters['filters'] ?? []);
    }

    private function buildParamsFromRequest(?Request $input)
    {
        $this->request = (is_null($input))
            ? Request::createFromGlobals()
            : $input;

        $this->excludes = Excludes::fromRequest($this->request);
        $this->fields = Fields::fromRequest($this->request);
        $this->includes = Includes::fromRequest($this->request);
        $this->pagination = Pagination::fromRequest($this->request);
        $this->sorting = Sorting::fromRequest($this->request);
        $this->filters = Filters::fromRequest($this->request);
    }
}
