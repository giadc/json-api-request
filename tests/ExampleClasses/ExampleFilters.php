<?php

namespace Giadc\JsonApiRequest\Tests;

use Giadc\JsonApiRequest\Filters\FilterManager;

class ExampleFilters extends FilterManager
{
    /**
     * @var array
     */
    protected $accepted = [
        'id'            => ['type' => 'id'],
        'name'          => ['type' => 'keyword'],
        'size'          => ['type' => 'combined', 'keys' => ['width', 'height'], 'separator' => 'x'],
        'relationships' => ['type' => 'keyword', 'key' => 'relationships.id', 'booleanOperator' => self::BOOLEAN_AND],
        'dates'         => ['type' => 'date', 'key' => 'runDate'],
    ];
}
