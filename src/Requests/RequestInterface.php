<?php
namespace Giadc\JsonApiRequest\Requests;

/**
 * Interface RequestInterface
 * @author David Hill
 */
interface RequestInterface
{
    public function getParamsArray();
    public function getQueryString();
}
