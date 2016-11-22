<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Giadc\JsonApiRequest\Tests\ExampleEntity;
use Giadc\JsonApiRequest\Tests\ExampleFilters;
use Giadc\JsonApiRequest\Tests\ExampleReadService;
use Giadc\JsonApiRequest\Tests\ExampleRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractReadServiceTest extends JsonApiRequestTestCase
{
    private $exampleRepository;

    public function setUp()
    {
        parent::setUp();

        $exampleRepository        = new ExampleRepository($this->getEntityManager());
        $this->exampleReadService = new ExampleReadService($exampleRepository, new ExampleFilters());
    }

    public function test_it_finds_an_entity_by_id()
    {
        $result = $this->exampleReadService->findById('1', ['relationships']);
        $this->assertInstanceOf(ExampleEntity::class, $result);
        $this->assertTrue($result->getRelationships()->isInitialized());
    }

    public function test_it_throws_an_exception_when_a_find_by_id_fails()
    {
        $this->expectException(NotFoundHttpException::class);
        $result = $this->exampleReadService->findById('qq');
    }

    public function test_it_finds_entities_by_array()
    {
        $result = $this->exampleReadService->findByArray(['1', '2'], 'id', ['relationships']);
        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertTrue($result->first()->getRelationships()->isInitialized());
    }

    public function test_it_finds_entities_by_field()
    {
        $result = $this->exampleReadService->findByField(10, 'width', ['relationships']);
        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertTrue($result->first()->getRelationships()->isInitialized());
    }

    public function test_it_paginates_entities()
    {
        $result = $this->exampleReadService->paginate(['relationships']);
        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(5, $result->count());
    }

    public function test_it_returns_all_entities()
    {
        $result = $this->exampleReadService->all(['relationships']);
        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertTrue($result->first()->getRelationships()->isInitialized());
    }
}
