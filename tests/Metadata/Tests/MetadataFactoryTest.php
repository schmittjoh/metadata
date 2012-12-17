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
}
