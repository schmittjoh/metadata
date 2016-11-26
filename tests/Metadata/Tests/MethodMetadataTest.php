<?php

namespace Metadata\Tests;

use Metadata\Tests\Fixtures\TestObject;
use Metadata\MethodMetadata;

class MethodMetadataTest extends \PHPUnit_Framework_TestCase
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
        $metadata->invoke($obj, array('foo'));
        $this->assertEquals('foo', $obj->getFoo());
    }
}