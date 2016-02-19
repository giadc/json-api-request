<?php
namespace Giadc\JsonApi\Serializers;

use ReflectionClass;
use NilPortugues\Api\JsonApi\Http\Factory\RequestFactory;
use NilPortugues\Api\JsonApi\JsonApiTransformer;
use NilPortugues\Api\JsonApi\Http\Request\Request;
use NilPortugues\Serializer\DeepCopySerializer As Serializer;

/**
 * Class DoctrineJsonApiSerializer.
 */
class DoctrineJsonApiSerializer extends Serializer
{
    /**
     * @var JsonApiTransformer
     */
    protected $serializationStrategy;

    /**
     * @param JsonApiTransformer $strategy
     */
    public function __construct(JsonApiTransformer $strategy)
    {
        parent::__construct($strategy);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value)
    {
        $request = RequestFactory::create();
        $this->filterOutResourceFields($request);
        $this->filterOutIncludedResources($request);

        return parent::serialize($value);
    }

    /**
     * Parse the data to be json encoded.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws SerializerException
     */
     protected function serializeData($value)
    {
        if ($value instanceof \Doctrine\ORM\PersistentCollection) {
            $value = $value->isInitialized() ? $value->toArray() : null;
        }

        if ($value instanceof \Doctrine\ORM\Tools\Pagination\Paginator) {
            $value = $value->getIterator()->getArrayCopy();
        }

        if (! $value instanceof \Doctrine\ORM\Proxy\Proxy && ! $value instanceof \Doctrine\ORM\EntityManager) {
            return parent::serializeData($value);
        }
    }

    /**
     * @param mixed           $value
     * @param string          $className
     * @param ReflectionClass $ref
     *
     * @return array
     */
    protected function serializeInternalClass($value, $className, ReflectionClass $ref)
    {
        return parent::serializeInternalClass($value, $className, $ref);
    }

    /**
     * @return JsonApiTransformer
     */
    public function getTransformer()
    {
        return $this->serializationStrategy;
    }

    /**
     * @param Request $request
     */
    private function filterOutIncludedResources(Request $request)
    {
        if ($include = $request->getIncludedRelationships()) {
            foreach ($include as $resource => $includeData) {

                if ($this->serializationStrategy->getMappingByAlias($resource) == null) continue;

                foreach ($this->serializationStrategy->getMappings() as $mapping) {
                    $mapping->filteringIncludedResources(true);

                    if (is_array($includeData)) {
                        foreach ($includeData as $subResource) {
                            $this->serializationStrategy->getMappingByAlias($subResource)->addIncludedResource(
                                $this->serializationStrategy->getMappingByAlias($resource)->getClassName()
                            );
                        }
                        break;
                    }

                    $mapping->addIncludedResource(
                        $this->serializationStrategy->getMappingByAlias($resource)->getClassName()
                    );
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    private function filterOutResourceFields(Request $request)
    {
        if ($filters = $request->getFields()) {
            foreach ($filters as $type => $properties) {
                foreach ($this->serializationStrategy->getMappings() as $mapping) {
                    if ($mapping->getClassAlias() === $type) {
                        $mapping->setFilterKeys($properties);
                    }
                }
            }
        }
    }
}
