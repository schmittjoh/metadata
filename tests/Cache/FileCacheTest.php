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

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/jms-' . md5(__CLASS__);
        if (is_dir($this->dir)) {
            array_map('unlink', glob($this->dir . '/*'));
        } else {
            mkdir($this->dir);
        }
    }

    /**
     * @param string $className
     *
     * @dataProvider classNameProvider
     */
    public function testLoadEvictPutClassMetadataFromInCache(string $className)
    {
        $cache = new FileCache($this->dir);

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
}
