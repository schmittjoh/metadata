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

    protected function setUp()
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

    public function provideCorruptedCache()
    {
        yield 'No return statement' => ['<?php $a = "foo";'];
        yield 'Syntax error' => ['<?php syntax error'];
    }

    /**
     * @dataProvider provideCorruptedCache
     */
    public function testNonReturningCache(string $fileContents)
    {
        $cache = new FileCache($this->dir);

        file_put_contents($this->dir . '/Metadata-Tests-Fixtures-TestObject.cache.php', $fileContents);

        $this->assertNull($cache->load(TestObject::class));
    }

    public function testLoadAnonymousClassMetadataFromCacheThrowsException()
    {
        $cache = new FileCache($this->dir);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path provided');

        $cache->load(get_class(new class {}));
    }

    public function testPutAnonymousClassMetadataInCacheThrowsException()
    {
        $cache = new FileCache($this->dir);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not write new cache file to');

        $cache->put(new ClassMetadata(get_class(new class {})));
    }

    public function testEvictAnonymousClassMetadataFromCacheThrowsException()
    {
        $cache = new FileCache($this->dir);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path provided');

        $cache->evict(get_class(new class {}));
    }
}
