<?php

namespace Metadata\Tests;

use Metadata\ClassMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject');

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->getName());
        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->getReflection()->getName());
    }

    public function testSerializeUnserialize()
    {
        $metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }
}