<?php

namespace Metadata\Tests;

use Metadata\Tests\Fixtures\TestObject;
use Metadata\MethodMetadata;

class MethodMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->class);
        $this->assertEquals('setFoo', $metadata->name);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new MethodMetadata('Metadata\Tests\Fixtures\TestObject', 'setFoo');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }
}
