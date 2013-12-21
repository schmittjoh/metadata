<?php

namespace Metadata\Tests\Cache;

use Metadata\ClassMetadata;
use Metadata\Cache\FileCache;

class FileCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadEvictPutClassMetadataFromInCache()
    {
        $cache = new FileCache(sys_get_temp_dir());

        $this->assertNull($cache->loadClassMetadataFromCache($refl = new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')));
        $cache->putClassMetadataInCache($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject'));

        $this->assertEquals($metadata, $cache->loadClassMetadataFromCache($refl));

        $cache->evictClassMetadataFromCache($refl);
        $this->assertNull($cache->loadClassMetadataFromCache($refl));
    }
}