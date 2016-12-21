<?php

namespace Giadc\JsonApiRequest\Tests;

use Giadc\JsonApiRequest\Filters\DoctrineFilterManager;

class ExampleFilters extends DoctrineFilterManager
{
    /**
     * @var array
     */
    protected $accepted = [
        'id'    => ['type' => 'id'],
        'name'  => ['type' => 'keyword'],
        'size'  => ['type' => 'combined', 'keys' => ['width', 'height'], 'separator' => 'x'],
        'dates' => ['type' => 'date', 'key' => 'runDate'],
    ];
}
