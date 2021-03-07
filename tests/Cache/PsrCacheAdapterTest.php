<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Metadata\Cache\PsrCacheAdapter;
use Metadata\ClassMetadata;
use Metadata\Tests\Driver\Fixture\A\A;
use Metadata\Tests\Driver\Fixture\B\B;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * @requires PHP 5.5
 */
class PsrCacheAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(CacheItem::class)) {
            $this->markTestSkipped('symfony/cache is not installed.');
        }
    }

    /**
     * @param string $className
     *
     * @dataProvider classNameProvider
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
            'anonymous class' => [
                get_class(new class {
                }),
            ],
        ];
    }

    public function testClear(): void
    {
        $pool = new ArrayAdapter();
        $cacheAdapter = new PsrCacheAdapter('metadata-test', $pool);

        $cacheAdapter->put(new ClassMetadata(A::class));
        $cacheAdapter->put(new ClassMetadata(B::class));
        self::assertTrue($pool->hasItem('metadata-testMetadata-Tests-Driver-Fixture-A-A'));
        self::assertTrue($pool->hasItem('metadata-testMetadata-Tests-Driver-Fixture-B-B'));

        self::assertTrue($cacheAdapter->clear());
        self::assertFalse($pool->hasItem('metadata-testMetadata-Tests-Driver-Fixture-A-A'));
        self::assertFalse($pool->hasItem('metadata-testMetadata-Tests-Driver-Fixture-B-B'));
    }
}
