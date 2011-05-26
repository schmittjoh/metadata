<?php

namespace Metadata\Tests;

use Metadata\ClassMetadata;
use Metadata\MetadataFactory;

class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMetadataForClass()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');

        $driver
            ->expects($this->at(0))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->returnCallback(function($class) {
                return new ClassMetadata($class->getName());
            }))
        ;
        $driver
            ->expects($this->at(1))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestParent')))
            ->will($this->returnCallback(function($class) {
                return new ClassMetadata($class->getName());
            }))
        ;

        $factory = new MetadataFactory($driver);
        $metadata = $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestParent');

        $this->assertInstanceOf('Metadata\ClassHierarchyMetadata', $metadata);
        $this->assertEquals(2, count($metadata->classMetadata));
    }

    public function testGetMetadataWithCache()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata = new ClassMetadata('Metadata\Tests\Fixtures\TestObject')))
        ;

        $factory = new MetadataFactory($driver);

        $cache = $this->getMock('Metadata\Cache\CacheInterface');
        $cache
            ->expects($this->once())
            ->method('loadClassMetadataFromCache')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->returnValue(null))
        ;
        $cache
            ->expects($this->once())
            ->method('putClassMetadataInCache')
            ->with($this->equalTo($metadata))
        ;
        $factory->setCache($cache);

        $this->assertSame($metadata, reset($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject')->classMetadata));
    }

    public function testGetMetadataReturnsNullIfNoMetadataIsFound()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;

        $factory = new MetadataFactory($driver);

        $this->assertNull($factory->getMetadataForClass('stdClass'));
    }
}