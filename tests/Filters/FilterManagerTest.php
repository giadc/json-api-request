<?php

use Doctrine\ORM\Tools\Setup;
use Giadc\JsonApiRequest\Requests\Filters;
use Giadc\JsonApiRequest\Tests\ExampleFilters;

class FilterManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filterManager = new ExampleFilters();
    }

    public function test_it_returns_the_params()
    {
        $this->assertTrue(is_array($this->filterManager->getParams()));
    }

    public function test_it_processes_the_filters()
    {
        $entityManager = EntityManagerFactory::createEntityManager();
        $qb            = $entityManager->createQueryBuilder();

        $qb->select('e')->from('Giadc\JsonApiRequest\Tests\ExampleEntity', 'e');

        $filters = new Filters([
            'id'            => '123',
            'name'          => 'Eduardo',
            'size'          => '10x10',
            'relationships' => 'relationshipOne,relationshipTwo',
            'dates'         => '01/01/2016-02/02/2020',
        ]);

        $result = $this->filterManager->process($qb, $filters)->getQuery();

        $expectedDQL = "SELECT e FROM Giadc\JsonApiRequest\Tests\ExampleEntity e LEFT JOIN e.relationships relationships WHERE e.id = ?1 AND e.name LIKE ?2 AND CONCAT(e.width, 'x', e.height) LIKE ?3 AND (relationships.id LIKE ?4 AND relationships.id LIKE ?5) AND (e.runDate BETWEEN ?6 AND ?7)";
        $this->assertEquals($expectedDQL, $result->getDql());

        $paramArray = $result->getParameters()->map(function ($parameter) {
            $value = $parameter->getValue();

            return ($value instanceof \DateTime)
                ? $value->format('Y-m-d H:i:s')
                : $value;
        })->toArray();

        $expectedParmArray = [
            '123',
            '%Eduardo%',
            '%10x10%',
            '%relationshipOne%',
            '%relationshipTwo%',
            '2016-01-01 00:00:00',
            '2020-02-02 23:59:59',
        ];
        $this->assertEquals($expectedParmArray, $paramArray);
    }
}
