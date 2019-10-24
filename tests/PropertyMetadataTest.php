<?php

declare(strict_types=1);

namespace Metadata\Tests;

use Metadata\PropertyMetadata;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    public function testConstructor()
    {
        $metadata = new PropertyMetadata(TestObject::class, 'foo');
        $this->assertEquals(Fixtures\TestObject::class, $metadata->class);
        $this->assertEquals('foo', $metadata->name);
    }

    public function testSerializeUnserialize()
    {
        $metadata = new PropertyMetadata(TestObject::class, 'foo');

        $this->assertEquals($metadata, unserialize(serialize($metadata)));
    }
}
