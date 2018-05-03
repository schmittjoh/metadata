<?php

namespace Metadata\Tests\Cache;

use Metadata\ClassMetadata;
use Metadata\Cache\DoctrineCacheAdapter;
use Doctrine\Common\Cache\ArrayCache;
use Metadata\Tests\Fixtures\TestObject;

/**
 * @requires PHP 5.4
 */
class DoctrineCacheAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Cache\Cache')) {
            $this->markTestSkipped('Doctrine\Common is not installed.');
        }
    }

    public function testLoadEvictPutClassMetadataFromInCache()
    {
        $cache = new DoctrineCacheAdapter('metadata-test', new ArrayCache());

        $this->assertNull($cache->load(TestObject::class));
        $cache->put($metadata = new ClassMetadata(TestObject::class));

        $this->assertEquals($metadata, $cache->load(TestObject::class));

        $cache->evict(TestObject::class);
        $this->assertNull($cache->load(TestObject::class));
    }
}
