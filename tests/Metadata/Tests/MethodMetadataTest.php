<?php

declare(strict_types=1);

namespace Metadata\Tests;

use Metadata\MethodMetadata;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;

class MethodMetadataTest extends TestCase
{
    public function testConstructor()
    {
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');
        $expectedReflector = new \ReflectionMethod('Metadata\Tests\Fixtures\TestObject', 'setFoo');
        $expectedReflector->setAccessible(true);

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->class);
        $this->assertEquals('setFoo', $metadata->name);
        $this->assertEquals($expectedReflector, $metadata->reflection);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }

    public function testInvoke()
    {
        $obj = new TestObject();
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');

        $this->assertNull($obj->getFoo());
        $metadata->invoke($obj, ['foo']);
        $this->assertEquals('foo', $obj->getFoo());
    }
}
