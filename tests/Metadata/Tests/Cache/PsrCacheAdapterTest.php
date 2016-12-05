<?php

namespace Metadata\Tests\Cache;

use Metadata\ClassMetadata;
use Metadata\Cache\PsrCacheAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @requires PHP 5.5
 */
class PsrCacheAdapterTest extends \PHPUnit_Framework_TestCase
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

        $this->assertNull($cache->loadClassMetadataFromCache($refl = new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')));
        $cache->putClassMetadataInCache($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject'));

        $this->assertEquals($metadata, $cache->loadClassMetadataFromCache($refl));

        $cache->evictClassMetadataFromCache($refl);
        $this->assertNull($cache->loadClassMetadataFromCache($refl));
    }
}
