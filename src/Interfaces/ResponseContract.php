<?php
namespace Giadc\JsonApi\Interfaces;

interface ResponseContract
{
    /*
     * Returns current statusCode
     *
     * @returns Integer
     */
    public function getStatusCode();

    /*
     * Sets status code
     *
     * @param Integer $statusCode
     * @returns Integer
     */
    public function setStatusCode($statusCode);

    /*
     * Return a new JSON response from the application.
     *
     * @param  array  $array
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function withArray(array $array, array $headers = array());

    /*
     * Return a new JSON error response from the application.
     *
     * @param  string $message
     * @param  string $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function withError($message, $errorCode);

    /*
     * Return a new JSON Response message
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function withMessage($message);

    /*
     * Return a new Delete Successful Response form application
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSuccessful($message = 'Delete Successful');

    /*
     * Return a new JSON response from an item
     *
     * @param  eloquent $item
     * @param  $callback
     * @return \Illuminate\Http\JsonResponse
     */
    public function withItem($item, $callback);

    /*
     * Return a new JSON response from a collection
     *
     * @param  eloquent collection $collection
     * @param  $callback
     * @return \Illuminate\Http\JsonResponse
     */
    public function withCollection($collection, $callback);

    /*
     * Return a new JSON response from a paginated collection
     *
     * @param  $paginator
     * @param  $callback
     * @return \Illuminate\Http\JsonResponse
     */
    public function withPagination($paginator, $callback);

    /*
     * Return a new JSON response forbidden error
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = 'Forbidden');

    /*
     * Return a new JSON response internal error
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalError($message = 'Internal Error');

    /*
     * Return a new JSON response not found
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound($message = 'Not Found');

    /*
     * Return a new JSON response unauthorized
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized($message = 'Unauthorized');

    /*
     * Return a new JSON response invalid credentials
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInvalidCredentials($message = 'Invalid Credentails');

    /*
     * Return a new JSON response invalid arguments
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorWrongArgs($message = 'Wrong Arguments');

    /*
     * Return a new JSON response Validation error
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorValidation($message = 'Validation Error');

    /*
     * Return a new JSON response not searchable
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotSearchable($message = 'Not Searchable');
}
