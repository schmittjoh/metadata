<?php

namespace Metadata\Tests\Cache;

use Metadata\ClassMetadata;
use Metadata\Cache\PsrCacheAdapter;
use Metadata\Tests\Fixtures\TestObject;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.5
 */
class PsrCacheAdapterTest extends TestCase
{
    public function setUp()
    {
        if (!class_exists('Symfony\Component\Cache\CacheItem')) {
            $this->markTestSkipped('symfony/cache is not installed.');
        }
    }

    public function testLoadEvictPutClassMetadataFromInCache()
    {
        $cache = new PsrCacheAdapter('metadata-test', new ArrayAdapter());

        $this->assertNull($cache->load(TestObject::class));
        $cache->put($metadata = new ClassMetadata(TestObject::class));

        $this->assertEquals($metadata, $cache->load(TestObject::class));

        $cache->evict(TestObject::class);
        $this->assertNull($cache->load(TestObject::class));
    }
}
