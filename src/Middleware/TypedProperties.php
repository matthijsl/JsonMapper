<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

class TypedProperties extends AbstractMiddleware
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $propertyMap, JsonMapperInterface $mapper): void
    {
        $propertyMap->merge($this->fetchPropertyMapForObject($object));
    }

    private function fetchPropertyMapForObject(ObjectWrapper $object): PropertyMap
    {
        if ($this->cache->has($object->getName())) {
            return $this->cache->get($object->getName());
        }

        $reflectionProperties = $object->getReflectedObject()->getProperties();
        $intermediatePropertyMap = new PropertyMap();

        foreach ($reflectionProperties as $reflectionProperty) {
            $type = $reflectionProperty->getType();

            if ($type === null) {
                continue;
            }

            $property = PropertyBuilder::new()
                ->setName($reflectionProperty->getName())
                ->setType($type->getName())
                ->setIsNullable($type->allowsNull())
                ->setVisibility(Visibility::fromReflectionProperty($reflectionProperty))
                ->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($object->getName(), $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }
}
