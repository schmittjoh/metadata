<?php

namespace Metadata\Tests;

use Metadata\PropertyMetadata;
use Metadata\MergeableClassMetadata;
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

    public function testGetMetadataForClassWhenMergeable()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');

        $driver
            ->expects($this->at(0))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->returnCallback(function($class) {
                return new MergeableClassMetadata($class->getName());
            }))
        ;
        $driver
            ->expects($this->at(1))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestParent')))
            ->will($this->returnCallback(function($class) {
                return new MergeableClassMetadata($class->getName());
            }))
        ;

        $factory = new MetadataFactory($driver);
        $metadata = $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestParent');

        $this->assertInstanceOf('Metadata\MergeableClassMetadata', $metadata);
        $this->assertEquals('Metadata\Tests\Fixtures\TestParent', $metadata->name);
    }

    public function testGetMetadataWithComplexHierarchy()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');

        $driver
            ->expects($this->any())
            ->method('loadMetadataForClass')
            ->will($this->returnCallback(function($class) {
                $metadata = new MergeableClassMetadata($class->name);

                switch ($class->name) {
                    case 'Metadata\Tests\Fixtures\ComplexHierarchy\BaseClass':
                        $metadata->propertyMetadata['foo'] = new PropertyMetadata($class->name, 'foo');
                        break;

                    case 'Metadata\Tests\Fixtures\ComplexHierarchy\SubClassA':
                        $metadata->propertyMetadata['bar'] = new PropertyMetadata($class->name, 'bar');
                        break;

                    case 'Metadata\Tests\Fixtures\ComplexHierarchy\SubClassB':
                        $metadata->propertyMetadata['baz'] = new PropertyMetadata($class->name, 'baz');
                        break;

                    default:
                        throw new \RuntimeException(sprintf('Unsupported class "%s".', $class->name));
                }

                return $metadata;
            }))
        ;

        $factory = new MetadataFactory($driver);

        $subClassA = $factory->getMetadataForClass('Metadata\Tests\Fixtures\ComplexHierarchy\SubClassA');
        $this->assertInstanceOf('Metadata\MergeableClassMetadata', $subClassA);
        $this->assertEquals(array('foo', 'bar'), array_keys($subClassA->propertyMetadata));

        $subClassB = $factory->getMetadataForClass('Metadata\Tests\Fixtures\ComplexHierarchy\SubClassB');
        $this->assertInstanceOf('Metadata\MergeableClassMetadata', $subClassB);
        $this->assertEquals(array('foo', 'baz'), array_keys($subClassB->propertyMetadata));
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


        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
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

    public function testGetMetadataWithInterfaces()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');

        $driver
            ->expects($this->at(3))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\ComplexHierarchy\SubClassA')))
        ;
        $driver
            ->expects($this->at(2))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\ComplexHierarchy\InterfaceB')))
        ;
        $driver
            ->expects($this->at(1))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\ComplexHierarchy\BaseClass')))
        ;
        $driver
            ->expects($this->at(0))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\ComplexHierarchy\InterfaceA')))
        ;

        $factory = new MetadataFactory($driver);
        $factory->setIncludeInterfaces(true);

        $factory->getMetadataForClass('Metadata\Tests\Fixtures\ComplexHierarchy\SubClassA');
    }

    public function testGetAllClassNames()
    {
        $driver = $this->getMock('Metadata\Driver\AdvancedDriverInterface');
        $driver
            ->expects($this->once())
            ->method('getAllClassNames')
            ->will($this->returnValue(array()));

        $factory = new MetadataFactory($driver);
        $this->assertSame(array(), $factory->getAllClassNames());
    }

    public function testGetAllClassNamesThrowsException()
    {
        $this->setExpectedException('RuntimeException');
        $factory = new MetadataFactory($this->getMock('Metadata\Driver\DriverInterface'));
        $factory->getAllClassNames();
    }

    public function testNotFoundMetadataIsCached()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');
        $driver
            ->expects($this->once()) // This is the important part of this test
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;

        $cachedMetadata = null;
        $cache = $this->getMock('Metadata\Cache\CacheInterface');
        $cache
            ->expects($this->any())
            ->method('loadClassMetadataFromCache')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->returnCallback(function () use (&$cachedMetadata) {
                return $cachedMetadata;
            }))
        ;
        $cache
            ->expects($this->once())
            ->method('putClassMetadataInCache')
            ->will($this->returnCallback(function ($metadata) use (&$cachedMetadata) {
                $cachedMetadata = $metadata;
            }))
        ;

        $factory = new MetadataFactory($driver);
        $factory->setCache($cache);
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject'));

        // We use another factory with the same cache, to simulate another request and skip the in memory
        $factory = new MetadataFactory($driver);
        $factory->setCache($cache);
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject'));
    }

    public function testNotFoundMetadataIsNotCachedInDebug()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');
        $driver
            ->expects($this->exactly(2))
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;

        $cachedMetadata = null;
        $cache = $this->getMock('Metadata\Cache\CacheInterface');
        $cache
            ->expects($this->any())
            ->method('loadClassMetadataFromCache')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->returnValue(null))
        ;
        $cache
            ->expects($this->never())
            ->method('putClassMetadataInCache')
        ;

        $factory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', true);
        $factory->setCache($cache);
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject'));

        // We use another factory with the same cache, to simulate another request and skip the in memory
        $factory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', true);
        $factory->setCache($cache);
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject'));
    }

    public function testLoadingCorruptMetadataFromCacheLoadsUsingDriver()
    {
        $driver = $this->getMock('Metadata\Driver\DriverInterface');
        $driver
            ->expects($this->exactly(1))
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;

        $cachedMetadata = null;
        $cache = $this->getMock('Metadata\Cache\CacheInterface');
        $cache
            ->expects($this->once())
            ->method('loadClassMetadataFromCache')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestObject')))
            ->will($this->throwException(new \ReflectionException()))
        ;

        $factory = new MetadataFactory($driver, 'Metadata\ClassHierarchyMetadata', true);
        $factory->setCache($cache);
        $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject');
        $this->assertNull($factory->getMetadataForClass('Metadata\Tests\Fixtures\TestObject'));
    }
}
