<?php

declare(strict_types=1);

namespace Metadata\Tests\Cache;

use Metadata\Cache\FileCache;
use Metadata\ClassMetadata;
use Metadata\Tests\Driver\Fixture\A\A;
use Metadata\Tests\Driver\Fixture\B\B;
use Metadata\Tests\Fixtures\TestObject;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    private $dir;
    private $nestedDir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/jms-' . md5(__CLASS__);
        $this->nestedDir = $this->dir . '/some-dir';
        if (is_dir($this->dir)) {
            if (is_dir($this->nestedDir)) {
                array_map('unlink', glob($this->nestedDir . '/*'));
                rmdir($this->nestedDir);
            }

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
            'TestObject'      => [TestObject::class],
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

    public function testPutCacheFileInNotExistingDirectory()
    {
        $cache = new FileCache($this->nestedDir);

        $reflectionClass = new \ReflectionClass('Metadata\Tests\Fixtures\TestObject');
        $cache->put($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject'));
        $this->assertEquals($metadata, $cache->load($reflectionClass->name));
    }

    public function testThrowExceptionIfCacheDirNotWritable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can\'t create directory for cache at "vfs://root/JMS"');

        $root = vfsStream::setup('root', 0555);
        $cache = new FileCache($root->url() . '/JMS');
    }

    public function testThrowExceptionIfCacheFilePathNotWritable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The directory "vfs://root" is not writable.');

        $root = vfsStream::setup('root', 0555);
        $cache = new FileCache($root->url());

        $cache->put($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestParent'));
    }

    public function testClear(): void
    {
        self::assertCount(0, glob($this->dir . '/*'));
        $cache = new FileCache($this->dir);

        $cache->put(new ClassMetadata(A::class));
        $cache->put(new ClassMetadata(B::class));
        self::assertCount(2, glob($this->dir . '/*'));

        self::assertTrue($cache->clear());
        self::assertCount(0, glob($this->dir . '/*'));
    }
}
