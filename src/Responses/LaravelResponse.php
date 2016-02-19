<?php
namespace Giadc\JsonApi\Responses;

use Giadc\JsonApi\Interfaces\ResponseContract;
use Giadc\JsonApi\Pagination\FractalDoctrinePaginatorAdapter as PaginatorAdapter;
use Giadc\JsonApi\Requests\RequestParams;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;

ini_set('xdebug.max_nesting_level', 200);

/**
 * Class Response
 *
 * @package App\Http\Responses
 */
class LaravelResponse implements ResponseContract
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var \Illuminate\Routing\UrlGenerator
     */
    public $url;

    /**
     * @var \League\Fractal\Manager
     */
    public $fractal;

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    public $responseFactory;

    protected $requestParams;

    const CODE_WRONG_ARGS = 'WRONG_ARGS';
    const CODE_NOT_FOUND = 'NOT_FOUND';
    const CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
    const CODE_UNAUTHORIZED = 'UNAUTHORIZED';
    const CODE_FORBIDDEN = 'FORBIDDEN';
    const CODE_VALIDATION_ERROR = 'VALIDATION_ERROR';
    const CODE_NOT_SEARCHABLE = 'NOT_SEARCHABLE';
    const CODE_INVALID_CREDENTAILS = 'INVALID CREDENTIALS';

    /**
     * @param \Illuminate\Routing\UrlGenerator $url
     * @param \League\Fractal\Manager $fractal
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     */
    public function __construct(UrlGenerator $url, Manager $fractal, ResponseFactory $response, RequestParams $requestParams)
    {
        $this->fractal = $fractal;
        $this->responseFactory = $response;
        $this->url = $url;
        $this->requestParams = $requestParams;

        $this->fractal->setSerializer(new JsonApiSerializer());

        if (isset($_GET['include'])) {
            $fractal->parseIncludes($_GET['include']);
        }
    }

    /**
     * Returns current statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets status code
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param array $array
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withArray(array $array, array $headers = array())
    {
        return $this->responseFactory->json($array, $this->statusCode, $headers);
    }

    /**
     * Return a new JSON error response from the application.
     *
     * @param $message
     * @param $errorCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withError($message, $errorCode)
    {
        if ($this->statusCode === 200) {
            trigger_error('Status set to 200..please try again.');
        }

        return $this->withArray(array(
            'errors' => array(
                array(
                    'code'      => $errorCode,
                    'status' => $this->statusCode,
                    'detail'   => $message,
                )
            )
        ));
    }

    /**
     * Returns a new JSON response with multiple errors
     *
     * @param  array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function withErrors($errors)
    {
        if ($this->statusCode === 200) {
            trigger_error('Status set to 200..please try again.');
        }

        return $this->withArray(array(
            'errors' => $errors
        ));
    }

    /**
     * Return a new JSON Response message
     *
     * @param $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withMessage($message)
    {
        return $this->withArray(array(
            'data' => array(
                'http_code' => $this->statusCode,
                'message'   => $message
            )
        ));
    }

    /**
     * Return a new Delete Successful Response form application
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSuccessful($message = 'Delete Successful')
    {
        return $this->withMessage($message);
    }

    /**
     * Return a new Create Successful Response form application
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSuccessful(array $headers = array())
    {
        $this->setStatusCode(201);

        return $this->responseFactory->json('', $this->statusCode, $headers);
    }


    /**
     * Return a new Update Successful Response form application
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSuccessful(array $headers = array())
    {
        return $this->success($headers);
    }

    /**
     * Returns with null data
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function withNull(array $headers = array())
    {
        $this->setStatusCode(200);
        $json = json_decode('{"data": null}');

        return $this->responseFactory->json($json, $this->statusCode, $headers);
    }

    /**
     * Returns a 204 no content
     *
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(array $headers = array())
    {
        $this->setStatusCode(204);

        return $this->responseFactory->json('', $this->statusCode, $headers);
    }

    /**
     * Return a new JSON response from an item
     *
     * @param $item
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withItem($item, $callback, $resourceKey = '')
    {
        $resource = new Item($item, $callback, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response from a collection
     *
     * @param $collection
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withCollection($collection, $callback, $resourceKey = '')
    {
        $resource = new FractalCollection($collection, $callback, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response from a paginated collection
     *
     * @param $paginator
     * @param $callback
     * @param string $resourceKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function withPagination($paginator, $callback, $resourceKey = '')
    {
        $resource = new FractalCollection($paginator, $callback, $resourceKey);
        $resource->setPaginator(new PaginatorAdapter($paginator, $this->requestParams));
        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Return a new JSON response forbidden error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Return a new JSON response internal error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->withError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Return a new JSON response unauthorized
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Return a new JSON response invalid credentials
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorInvalidCredentials($message = 'Invalid Credentails')
    {
        return $this->setStatusCode(401)->withError($message, self::CODE_INVALID_CREDENTAILS);
    }

    /**
     * Return a new JSON response invalid arguments
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * Returns a new JSON response with multiple validation errors
     *
     * @param  array $messages
     * @return \Illuminate\Http\Resposne
     */
    public function errorsValidation($messages)
    {
        $errors = [];
        
        foreach ($messages as $message) {
            $errors[] = array(
                'code'   => self::CODE_VALIDATION_ERROR,
                'status' => 400,
                'detail' => $message,
            );
        }

        return $this->setStatusCode(400)->withErrors($errors);
    }    

    /**
     * Return a new JSON response Validation error
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorValidation($message = 'Validation Error')
    {
        if ($message instanceof Illuminate\Support\MessageBag) {
            $message = $message->__toString();
        }

        return $this->setStatusCode(400)->withError($message, self::CODE_VALIDATION_ERROR);
    }

    /**
     * Return a new JSON response not searchable
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorNotSearchable($message = 'Not Searchable')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_NOT_SEARCHABLE);
    }
}
