<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Metadata\Cache\DoctrineCacheAdapter;
use Metadata\ClassMetadata;
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
}
