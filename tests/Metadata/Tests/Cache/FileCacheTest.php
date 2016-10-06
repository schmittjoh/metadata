<?php

namespace Metadata\Tests\Cache;

use Metadata\ClassMetadata;
use Metadata\Cache\FileCache;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileCacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var vfsStreamDirectory */
    private $root;
    
    public function setUp()
    {
        $this->root = vfsStream::setup('tmp', 0777);
    }

    public function testLoadEvictPutClassMetadataFromInCache()
    {
        $cache = new FileCache($this->root->url());

        $reflectionClass = new \ReflectionClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($cache->loadClassMetadataFromCache($reflectionClass));
        $cache->putClassMetadataInCache($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject'));

        $this->assertEquals($metadata, $cache->loadClassMetadataFromCache($reflectionClass));

        $cache->evictClassMetadataFromCache($reflectionClass);
        $this->assertNull($cache->loadClassMetadataFromCache($reflectionClass));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThrowExceptionIfNotWritable()
    {
        $this->root->chmod(0555);
        $cache = new FileCache($this->root->url());

        $cache->putClassMetadataInCache($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestParent'));
    }
}