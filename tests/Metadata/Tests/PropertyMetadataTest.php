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
    }

    public function testSerializeUnserialize()
    {
        $metadata = new PropertyMetadata('Metadata\Tests\Fixtures\TestObject', 'foo');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }
}
