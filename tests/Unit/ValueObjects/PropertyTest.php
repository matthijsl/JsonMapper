<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\ValueObjects;

use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testGettersReturnConstructorValues(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC());

        self::assertSame('id', $property->getName());
        self::assertSame('int', $property->getType());
        self::assertFalse($property->isNullable());
        self::assertTrue($property->getVisibility()->equals(Visibility::PUBLIC()));
    }

    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testPropertyCanBeConvertedToBuilderAndBack(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC());
        $builder = $property->asBuilder();

        self::assertEquals($property, $builder->build());
    }

    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testCanBeConvertedToJson(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC());

        $propertyAsJson = json_encode($property);

        self::assertIsString($propertyAsJson);
        self::assertJsonStringEqualsJsonString(
            '{"name":"id","type":"int","isNullable":false,"visibility":"public"}',
            (string) $propertyAsJson
        );
    }
}
