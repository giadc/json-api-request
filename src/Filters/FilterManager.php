<?php
namespace Giadc\JsonApi\Filters;

use Doctrine\ORM\QueryBuilder;
use Giadc\JsonApi\Requests\Filters;
use Giadc\JsonApi\Repositories\Processors;

/**
 * Class FilterManager
 */
abstract class FilterManager
{
    use Processors;

    /**
     * @var array
     */
    protected $accepted = [];

    /**
     * @var string
     */
    protected $searchDql = '';

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var int
     */
    protected $paramInt = 1;

    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @param $filters
     * @throws \Exception
     */
    public function process(QueryBuilder $qb, Filters $filters)
    {
        $this->qb = $qb;

        foreach ($filters->toArray() as $key => $data) {
            if (array_key_exists($key, $this->accepted)) {
                $this->processFilter($key, $data);
            }
        }

        foreach ($this->getParams() as $key => $value) {
            $this->qb->setParameter($key, $value);
        }

        return $this->qb;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $key
     * @param $data
     * @throws \Exception
     */
    private function processFilter($key, $data)
    {
        $info = $this->accepted[$key];
        $key = $this->getValidKey($info, $key);
        $type = !array_key_exists('type', $info) ? 'id' : $info['type'];
        $method = [$this, $type . 'Builder'];

        if (!is_callable($method)) {
            throw new \Exception('Invalid Type');
        }

        call_user_func($method, $data, $key);
    }

    private function getValidKey($info, $key)
    {
        return $key = isset($info['key']) ? $info['key'] : $key;
    }

    /**
     * @param $data
     * @param $key
     */
    private function idBuilder($data, $key)
    {
        if (is_array($key)) {
            $this->buildMultiple($data, $key);
        } else {
            $this->buildSingle($data, $key);
        }
    }

    private function getKey($key)
    {
        if (strpos($key, ".") !== false) {
            $this->addInclude(explode('.', $key)[0]);
            return $key;
        }

        return 'e.' . $key;
    }

    /**
     * @param $data
     * @param $keys
     */
    private function buildMultiple($data, $keys)
    {
        $conditions = [];

        foreach ($keys as $field) {
            $conditions[] = $this->qb->expr()
                ->in($this->getKey($field), '?' . $this->paramInt);
        }

        $orX = $this->qb->expr()->orX();
        $orX->addMultiple($conditions);

        $this->qb->add('where', $orX);
        $this->setParameter($this->paramInt, $data);
    }

    /**
     * @param $data
     * @param $key
     */
    private function buildSingle($data, $key)
    {
        if (is_array($data) && count($data) > 1) {
            $this->qb->add('where', $this->qb->expr()->in($this->getKey($key), '?' . $this->paramInt));
            $this->setParameter($this->paramInt, $data);

            return;
        }

        $sql = $this->getKey($key) . " = ?" . $this->paramInt;

        $this->qb->andWhere($sql);
        $this->setParameter($this->paramInt, $data[0]);
    }

    /**
     * @param $data
     * @param $key
     */
    private function keywordBuilder($data, $keys)
    {
        if (is_array($keys)) {
            $conditions = $this->buildMultipleConditions($data, $keys);
        } else {
            $conditions = $this->buildSingleConditions($data, $keys);
        }

        $orX = $this->qb->expr()->orX();
        $orX->addMultiple($conditions);

        $this->qb->add('where', $orX);
    }

    private function buildMultipleConditions($data, $keys)
    {
        $conditions = [];

        foreach ($keys as $key) {
            $conditions = array_merge($conditions, $this->buildSingleConditions($data, $key));
        }

        return $conditions;
    }

    private function buildSingleConditions($data, $key)
    {
        $conditions = [];

        foreach($data as $field) {
            $conditions[] = $this->qb->expr()
                ->like($this->getKey($key), '?' . $this->paramInt);

            $this->setParameter($this->paramInt, '%' . $field . '%');
        }

        return $conditions;
    }

    /**
     * @param $data
     * @param $key
     * @throws \Exception
     */
    private function dateBuilder($data, $keys)
    {
        if (! is_array($keys))
           return $this->qb->andWhere($this->buildSingleDate($data, $keys));

        $sql = '';

        foreach ($keys as $key) {
            if (! $sql == '')
                $sql.= ' OR ';
            $sql .= $this->buildSingleDate($data, $key);
        }

       return $this->qb->andWhere($sql);
    }

    private function buildSingleDate($data, $key)
    {
        if (is_array($data))
            $data = $data[0];

        $dates = explode('-', $data);

        if (count($dates) < 2) {
            $now = new \DateTime();
            $dates[1] = $now->format('m/d/Y');
        }

        if (!$this->validateDate($dates[0]) || !$this->validateDate($dates[1])) {
            throw new \Exception('Invalid Dates Provided');
        }

        $sql = $this->getKey($key) . " BETWEEN ?" . $this->paramInt;
        $this->setParameter($this->paramInt, \DateTime::createFromFormat('m/d/Y H:i:s', $dates[0] . '00:00:00'));

        $sql .= " AND ?" . $this->paramInt;
        $this->setParameter($this->paramInt, \DateTime::createFromFormat('m/d/Y H:i:s', $dates[1] . '23:59:59'));

        return $sql;
    }

    /**
     * @param $key
     * @param $value
     */
    private function setParameter($key, $value)
    {
        $this->params[$key] = $value;
        $this->paramInt = $this->paramInt + 1;
    }

    /**
     * @param $date
     * @return bool
     */
    private function validateDate($date)
    {
        $d = \DateTime::createFromFormat('m/d/Y', $date);

        return $d && $d->format('m/d/Y') == $date;
    }

    private function addInclude($include)
    {
        if (! $this->hasInclude($include))
            throw new \Exception('Invalid Include: ' . $include);

        if ($this->includeExists($include))
            return;

        $this->qb->leftJoin('e.' . $include, $include);
        $this->qb->addSelect($include);
    }

    private function includeExists($include)
    {
        foreach ($this->qb->getDQLPart('join') as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $include) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function hasInclude($include)
    {
        $associations = $this->getClassMetadata()->getAssociationMappings();

        return array_key_exists($include, $associations);
    }

    protected function getClassMetadata()
    {
        $class = $this->qb->getDQLPart('from')[0]->getFrom();
        return $this->qb->getEntityManager()->getClassMetadata($class);
    }
}
