# JSON API Request
A package for automating pagination, filtering, sorting, and includes when working with the [JSON API](http://jsonapi.org/) standard.


## Installation
`composer install giadc/json-api-request`


## Basic Usage
### RequestParams
Request Params provides objects for pulling/modifying HttpFoundation Requests that compile with JSON API V1.0.

Example: 
```http
GET /articles?include=author,comments.author&page[number]=3&page[size]=20&filter[author]=frank&sort=-created,title
```
```php
use Giadc\JsonApiRequest\Requests\RequestParams;

// By default it creates params from Globals 
$request = new RequestParams();

// Get Includes
$request->getIncludes()->toArray();
// outputs: ['author', 'comments.author']

// Get Page Details
$request->getPageDetails()->toArray();
// outputs: ['page' => ['number => 3, 'size' => 20]]

// Get Sorting
$request->getSortDetails()->toArray();
// outputs: [['field' => 'title', 'direction' => 'ASC'], ['field' => 'created', 'direction' => 'DESC']]

// Get Filters
$request->getFiltersDetails()->toArray();
// outputs: ['author' => ['frank']]

// Get Full Pagination
$request->getFullPagination();
// outputs: [{Pagination}, {Includes}, {Sorting}, {Filters}]
```

### Request Objects
All Objects implements the RequestInterface and give access the following methods: `getParamsArray()`, `getQueryString()`
#### Includes
```php
$includes = new Includes(['author', 'comments.author');
$includes->add('site');
$includes->getQueryString();
//outputs: 'include=author,comments.author,site'
```

#### Pagination
Pagination sets a defaults the pages size if not provided. It will Also allow you to set max it is able to return by settings the `PAGINATION_DEFAULT` and `PAGINATION_MAX` env variables.
```php
$pagination = new Pagination(2, 20);
$pagination->getQueryString();
//outputs: 'page[number]=2&page[size]=20'

// PAGINATION_MAX=25
$pagination = new Pagination(2, 1000);
$pagination->getQueryString();
//outputs: 'page[number]=2&page[size]=25'
```

#### Sorting
```php
$sorting = new Sorting('-created,title');
$sorting->setSorting();
$sorting->getQueryString('created,-title');
//outputs: 'sort=created,title'
```

#### Filters
```php
$filters = new Filters(['author' => 'frank');
$filters->addFilter('author', 'bob');
$filters->getQueryString();
//outputs: 'filter[author]=frank,bob'
```
