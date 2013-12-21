<?php

namespace Metadata\Tests;

use Metadata\MergeableClassMetadata;

class MergeableClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $parentMetadata = new MergeableClassMetadata('Metadata\Tests\Fixtures\TestParent');
        $parentMetadata->propertyMetadata['foo'] = 'bar';
        $parentMetadata->propertyMetadata['baz'] = 'baz';
        $parentMetadata->methodMetadata['foo'] = 'bar';
        $parentMetadata->createdAt = 2;
        $parentMetadata->fileResources[] = 'foo';

        $childMetadata = new MergeableClassMetadata('Metadata\Tests\Fixtures\TestObject');
        $childMetadata->propertyMetadata['foo'] = 'baz';
        $childMetadata->methodMetadata['foo'] = 'baz';
        $childMetadata->createdAt = 1;
        $childMetadata->fileResources[] = 'bar';

        $parentMetadata->merge($childMetadata);
        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $parentMetadata->name);
        $this->assertEquals('Metadata\Tests\Fixtures\TestObject', $parentMetadata->reflection->name);
        $this->assertEquals(array('foo' => 'baz', 'baz' => 'baz',), $parentMetadata->propertyMetadata);
        $this->assertEquals(array('foo' => 'baz',), $parentMetadata->methodMetadata);
        $this->assertEquals(1, $parentMetadata->createdAt);
        $this->assertEquals(array('foo', 'bar'), $parentMetadata->fileResources);
    }
}