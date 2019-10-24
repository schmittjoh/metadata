<?php

declare(strict_types=1);

namespace Metadata\Tests;

use Metadata\ClassMetadata;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    public function testConstructor()
    {
        $metadata = new ClassMetadata(TestObject::class);

        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $metadata->name);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new ClassMetadata(TestObject::class);

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }

    public function testIsFresh()
    {
        $ref = new \ReflectionClass(TestObject::class);
        touch($ref->getFilename());
        sleep(2);

        $metadata = new ClassMetadata($ref->name);
        $metadata->fileResources[] = $ref->getFilename();
        $this->assertTrue($metadata->isFresh());

        sleep(2);
        clearstatcache(!!$ref->getFilename());
        touch($ref->getFilename());
        $this->assertFalse($metadata->isFresh());
    }
}
