<?php
namespace App\Common\Repositories;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Giadc\JsonApiRequest\Repositories\Processors;
use Giadc\JsonApiRequest\Requests\Includes;
use Giadc\JsonApiRequest\Requests\Pagination;
use Giadc\JsonApiRequest\Requests\Sorting;
use Giadc\JsonApiRequest\Requests\Filters;

abstract class AbstractJsonApiDoctrineRepository
{
    use Processors;

    protected $class;

    protected function logDql()
    {
        $this->em
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    }

    protected function getDefaultSort()
    {
        return [];
    }

    public function paginateAll(Pagination $page, Includes $includes, Sorting $sort, Filters $filters)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
            ->from($this->class, 'e');

        $qb = $this->processSorting($qb, $sort);
        $qb = $this->processIncludes($qb, $includes);

        if (isset($this->filters))
            $qb = $this->filters->process($qb, $filters);

        return $this->paginate($qb, $page);
    }

    public function findById($value, Includes $includes = null)
    {
        $results = $this->findByColumn('id', $value, $includes);
        return $results == null ? null : $results[0];
    }

    public function findByColumn($field, $value, Includes $includes = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
            ->from($this->class, 'e')
            ->where('e.'. $field . ' = ?1');

        if ($includes) {
            $qb = $this->processIncludes($qb, $includes);
        }

        $qb->setParameter(1, $value);

        return $qb->getQuery()->getResult();
    }

    public function findByArray($array, $column = 'id', Includes $includes)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e');
        $qb->from($this->class, 'e');
        $qb->where($qb->expr()->in('e.' . $column, $array));

        $qb = $this->processIncludes($qb, $includes);

        //ArrayCollection
        return $qb->getQuery()->getResult();
    }

    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Updates or creates an Entity
     *
     * @param $entity
     * @return void
     */
    public function createOrUpdate($entity)
    {
        $this->isValidEntity($entity);

        $e = $this->findById($entity->getId());


        if ($e == null) {
            return $this->add($entity);
        }

        return $this->update($entity);
    }

    /**
     * Update an existing Entity
     *
     * @param $entity
     * @return void
     */
    public function update($entity, $mute = false)
    {
        $this->isValidEntity($entity);

        $this->em->merge($entity);

        if (!$mute) {
            $this->em->flush();
        }
    }

    public function add($entity, $mute = false)
    {
        $this->isValidEntity($entity);

        $this->em->persist($entity);

        if (!$mute) {
            $this->em->flush();
        }
    }

    public function delete($entity, $force = false, $mute = false)
    {
        $this->isValidEntity($entity);

        $this->em->remove($entity);

        if (!$mute) {
            $this->em->flush();
        }
    }

    protected function isValidEntity($entity)
    {
        if(!is_a($entity, $this->class))
            throw new \Exception('Invalid Entity: '.get_class($entity));
    }

    public function enableFilters( $options )
    {
        if( ! in_array('deleted', $options)){
            $filter = $this->em->getConfiguration()->addFilter("deleted", "App\Common\Filters\DoctrineDeletedFilter");

            $filter = $this->em->getFilters()->enable('deleted');
        }

        if( ! in_array('inactive', $options)){
            $filter = $this->em->getConfiguration()->addFilter("active", "App\Common\Filters\DoctrineActiveFilter");

            $filter = $this->em->getFilters()->enable('active');
        }

        if( in_array('showcase', $options)){
            $filter = $this->em->getConfiguration()->addFilter("showcase", "App\Common\Filters\DoctrineShowcaseFilter");

            $filter = $this->em->getFilters()->enable('showcase');
        }

        if( in_array('form', $options)){
            $filter = $this->em->getConfiguration()->addFilter("form", "App\Common\Filters\DoctrineFormFilter");

            $filter = $this->em->getFilters()->enable('form');
        }
    }
}
