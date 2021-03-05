<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Metadata\Cache\DoctrineCacheAdapter;
use Metadata\ClassMetadata;
use Metadata\Tests\Driver\Fixture\A\A;
use Metadata\Tests\Driver\Fixture\B\B;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP 5.4
 */
class DoctrineCacheAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!interface_exists('Doctrine\Common\Cache\Cache')) {
            $this->markTestSkipped('Doctrine\Common is not installed.');
        }
    }

    /**
     * @param string $className
     *
     * @dataProvider classNameProvider
     */
    public function testLoadEvictPutClassMetadataFromInCache(string $className)
    {
        $cache = new DoctrineCacheAdapter('metadata-test', new ArrayCache());

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
        $cache = new ArrayCache();
        $cacheAdapter = new DoctrineCacheAdapter('', $cache);

        $cacheAdapter->put(new ClassMetadata(A::class));
        $cacheAdapter->put(new ClassMetadata(B::class));
        self::assertTrue($cache->contains(A::class));
        self::assertTrue($cache->contains(B::class));

        self::assertTrue($cacheAdapter->clear());
        self::assertFalse($cache->contains(A::class));
        self::assertFalse($cache->contains(B::class));
    }
}
