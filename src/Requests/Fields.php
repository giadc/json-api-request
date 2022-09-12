<?php

namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

class Fields extends AbstractArrayParams
{
    protected static function paramKey(): string
    {
        return 'fields';
    }

    public static function fromRequest(Request $request): self
    {
        $items = $request->query->all(static::paramKey());

        return new self($items ?? []);
    }
}
