<?php
namespace Giadc\JsonApiRequest\Requests;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RequestInterface
 * @author David Hill
 */
interface RequestInterface
{
    public static function fromRequest(Request $request): self;
    public function getParamsArray();
    public function getQueryString();
}
