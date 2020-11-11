<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Excludes extends AbstractArrayParams
{
    protected static function paramKey(): string
    {
        return 'excludes';
    }

    public static function fromRequest(Request $request): self
    {
        $items = $request->query->get(static::paramKey());

        return new self($items ?? []);
    }
}
