<?php

declare(strict_types=1);

namespace Metadata\Tests;

use Metadata\MergeableClassMetadata;
use Metadata\Tests\Fixtures\TestObject;
use Metadata\Tests\Fixtures\TestParent;
use PHPUnit\Framework\TestCase;

class MergeableClassMetadataTest extends TestCase
{
    public function testMerge()
    {
        $parentMetadata = new MergeableClassMetadata(TestParent::class);
        $parentMetadata->propertyMetadata['foo'] = 'bar';
        $parentMetadata->propertyMetadata['baz'] = 'baz';
        $parentMetadata->methodMetadata['foo'] = 'bar';
        $parentMetadata->createdAt = 2;
        $parentMetadata->fileResources[] = 'foo';

        $childMetadata = new MergeableClassMetadata(TestObject::class);
        $childMetadata->propertyMetadata['foo'] = 'baz';
        $childMetadata->methodMetadata['foo'] = 'baz';
        $childMetadata->createdAt = 1;
        $childMetadata->fileResources[] = 'bar';

        $parentMetadata->merge($childMetadata);
        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $parentMetadata->name);
        $this->assertEquals(['foo' => 'baz', 'baz' => 'baz'], $parentMetadata->propertyMetadata);
        $this->assertEquals(['foo' => 'baz'], $parentMetadata->methodMetadata);
        $this->assertEquals(1, $parentMetadata->createdAt);
        $this->assertEquals(['foo', 'bar'], $parentMetadata->fileResources);
    }
}
