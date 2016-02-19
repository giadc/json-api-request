<?php
namespace App\Common\Interfaces;

use Giadc\JsonApi\Requests\Includes;
use Giadc\JsonApi\Requests\Pagination;
use Giadc\JsonApi\Requests\Sorting;
use Giadc\JsonApi\Requests\Filters;

interface AbstractJsonApiRepositoryInterface
{
    public function paginateAll(Pagination $page, Includes $includes, Sorting $sort, Filters $filters);
    public function findById($value, Includes $includes);
    public function findByColumn($field, $value, Includes $includes);
    public function findByArray($array, $column = 'id', Includes $includes);
    public function createOrUpdate($entity);
    public function add($entity);
    public function update($entity);
    public function delete($entity, $force = false);
    public function enableFilters($options);
    public function flush();
}
