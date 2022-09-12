<?php

namespace Giadc\JsonApiRequest\Exceptions;

use Exception;
use Throwable;

class InvalidSortDirectionsException extends Exception
{
    public function __construct(string $field, string $direction, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Invalid direction, %s, for %s',
            $direction,
            $field
        );

        parent::__construct($message, $code, $previous);
    }
}
