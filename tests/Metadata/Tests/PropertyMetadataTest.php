<?php

namespace Metadata\Tests;

use Metadata\Tests\Fixtures\TestObject;
use Metadata\PropertyMetadata;

class PropertyMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $metadata = new PropertyMetadata('Metadata\Tests\Fixtures\TestObject', 'foo');

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->class);
        $this->assertEquals('foo', $metadata->name);
        $this->assertEquals(new \ReflectionProperty('Metadata\Tests\Fixtures\TestObject', 'foo'), $metadata->reflection);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new PropertyMetadata('Metadata\Tests\Fixtures\TestObject', 'foo');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }

    public function testSetGetValue()
    {
        $obj = new TestObject();
        $metadata = new PropertyMetadata('Metadata\Tests\Fixtures\TestObject', 'foo');

        $this->assertNull($metadata->getValue($obj));
        $metadata->setValue($obj, 'foo');
        $this->assertEquals('foo', $metadata->getValue($obj));
    }
}