<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Filters extends AbstractArrayParams
{
    protected static function paramKey(): string
    {
        return 'filter';
    }

    public static function fromRequest(Request $request): self
    {
        $items = $request->query->get(static::paramKey());

        return new self($items ?? []);
    }
}
