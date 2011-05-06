<?php

namespace Metadata\Tests;

use Metadata\Tests\Fixtures\TestObject;

use Metadata\MethodMetadata;

class MethodMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->getClass());
        $this->assertEquals('setFoo', $metadata->getName());
        $this->assertEquals(new \ReflectionMethod('Metadata\Tests\Fixtures\TestObject', 'setFoo'), $metadata->getReflection());
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