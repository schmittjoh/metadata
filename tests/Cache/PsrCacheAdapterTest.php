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

    /**
     * @dataProvider classNameProvider
     * @param string $className
     */
    public function testLoadEvictPutClassMetadataFromInCache(string $className)
    {
        $cache = new PsrCacheAdapter('metadata-test', new ArrayAdapter());

        $this->assertNull($cache->load($className));
        $cache->put($metadata = new ClassMetadata($className));

        $this->assertEquals($metadata, $cache->load($className));

        $cache->evict($className);
        $this->assertNull($cache->load($className));
    }

    public function classNameProvider()
    {
        return [
            'TestObject' => [TestObject::class],
            'anonymous class' => [get_class(new class {})]
        ];
    }
}
