<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Metadata\Cache\FileCache;
use Metadata\ClassMetadata;
use Metadata\Tests\Fixtures\TestObject;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    private $dir;

    public function setUp()
    {
        $this->dir = sys_get_temp_dir() . '/jms-' . md5(__CLASS__);
        if (is_dir($this->dir)) {
            array_map('unlink', glob($this->dir . '/*'));
        } else {
            mkdir($this->dir);
        }
    }

    public function testLoadEvictPutClassMetadataFromInCache()
    {
        $cache = new FileCache($this->dir);

        $this->assertNull($cache->load(TestObject::class));
        $cache->put($metadata = new ClassMetadata(TestObject::class));

        $this->assertEquals($metadata, $cache->load(TestObject::class));

        $cache->evict(TestObject::class);
        $this->assertNull($cache->load(TestObject::class));
    }
}
