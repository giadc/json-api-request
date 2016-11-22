<?php
namespace Giadc\JsonApiRequest\Interfaces;

use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\Sorting;

interface AbstractJsonApiRepositoryInterface
{
    public function paginateAll(Pagination $page, Includes $includes, Sorting $sort, Filters $filters);
    public function findById($value, Includes $includes);
    public function findByField($value, $field, Includes $includes);
    public function findByArray($array, $column = 'id', Includes $includes);
    public function createOrUpdate($entity);
    public function add($entity);
    public function update($entity);
    public function delete($entity, $force = false);
    public function flush();
    public function clear();
}
