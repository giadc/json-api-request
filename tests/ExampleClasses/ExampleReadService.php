<?php

namespace Giadc\JsonApiRequest\Tests;

use Giadc\JsonApiRequest\Services\AbstractReadService;
use Giadc\JsonApiRequest\Tests\ExampleRepository;

class ExampleReadService extends AbstractReadService
{
    public function __construct(ExampleRepository $exampleRepo)
    {
        $this->initialize($exampleRepo, 'Example Entity');
    }
}
