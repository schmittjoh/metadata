<?php

namespace Metadata\Tests;

use Metadata\ClassMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject');

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->name);
        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->reflection->name);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }

    public function testIsFresh()
    {
        $ref = new \ReflectionClass('Metadata\Tests\Fixtures\TestObject');
        touch($ref->getFilename());
        sleep(2);

        $metadata = new ClassMetadata($ref->name);
        $metadata->fileResources[] = $ref->getFilename();
        $this->assertTrue($metadata->isFresh());

        sleep(2);
        clearstatcache($ref->getFilename());
        touch($ref->getFilename());
        $this->assertFalse($metadata->isFresh());
    }
}
