<?php
namespace Giadc\JsonApi\Repositories;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Giadc\JsonApi\Requests\Includes;
use Giadc\JsonApi\Requests\Pagination;
use Giadc\JsonApi\Requests\Sorting;

trait Processors
{
    protected function processIncludes(QueryBuilder $qb, Includes $includes)
    {
        $includes = $includes->toArray();

        foreach ($includes as $key => $include) {
            if (! $this->hasInclude($include) || $this->includeExists($qb, $include))
                continue;

            $qb->leftJoin('e.' . $include, $include);
            $qb->addSelect($include);
        }

        return $qb;
    }

    private function includeExists(QueryBuilder $qb, $include)
    {
        foreach ($qb->getDQLPart('join') as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $include) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function processSorting(QueryBuilder $qb, Sorting $sort)
    {
        $sortArray = $sort->toArray();

        if (empty(array_filter($sortArray)))
            $sortArray = $this->getDefaultSort();

        foreach ( $sortArray as $key => $field) {
            if ($this->hasField($field['field']))
                $qb->addOrderBy('e.' . $field['field'], $field['direction']);
        }

        return $qb;
    }

    public function paginate(QueryBuilder $query, Pagination $page)
    {
        $query = $query->setParameters($query->getParameters())
                       ->setFirstResult($page->getOffset() * $page->getPageSize())
                       ->setMaxResults($page->getPageSize());

        return new Paginator($query, true);
    }

    protected function hasField($field)
    {
        $fields = $this->getClassMetadata($this->class);

        return $fields->hasField($field);
    }

    protected function hasInclude($include)
    {
        $associations = $this->getClassMetadata()->getAssociationMappings();

        return array_key_exists($include, $associations);
    }

    protected function getClassMetadata()
    {
         return $this->em->getClassMetadata($this->class);
    }
}
