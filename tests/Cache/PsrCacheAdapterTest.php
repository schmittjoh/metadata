<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Metadata\Cache\PsrCacheAdapter;
use Metadata\ClassMetadata;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * @requires PHP 5.5
 */
class PsrCacheAdapterTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists(CacheItem::class)) {
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
