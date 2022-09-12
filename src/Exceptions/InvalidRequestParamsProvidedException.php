<?php

namespace Giadc\JsonApiRequest\Exceptions;

use Exception;
use Giadc\JsonApiRequest\Requests\RequestParams;
use Throwable;

class InvalidRequestParamsProvidedException extends Exception
{
    public function __construct(array $invalidKeys, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Invalid Criteria: %s. Valid keys are %s.',
            implode(', ', $invalidKeys),
            implode(', ', RequestParams::VALID_PARAM_KEYS)
        );

        parent::__construct($message, $code, $previous);
    }
}
