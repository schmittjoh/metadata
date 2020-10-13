<?php

declare(strict_types=1);

namespace Metadata\Tests;

use Metadata\Cache\CacheInterface;
use Metadata\ClassHierarchyMetadata;
use Metadata\ClassMetadata;
use Metadata\Driver\AdvancedDriverInterface;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Metadata\MetadataFactory;
use Metadata\PropertyMetadata;
use Metadata\Tests\Fixtures\ComplexHierarchy\BaseClass;
use Metadata\Tests\Fixtures\ComplexHierarchy\InterfaceA;
use Metadata\Tests\Fixtures\ComplexHierarchy\InterfaceB;
use Metadata\Tests\Fixtures\ComplexHierarchy\SubClassA;
use Metadata\Tests\Fixtures\TestObject;
use Metadata\Tests\Fixtures\TestParent;
use PHPUnit\Framework\TestCase;

class MetadataFactoryTest extends TestCase
{
    public function testGetMetadataForClass()
    {
        $driver = $this->createMock(DriverInterface::class);

        $driver
            ->expects($this->exactly(2))
            ->method('loadMetadataForClass')
            ->withConsecutive(
                [$this->equalTo(new \ReflectionClass(TestObject::class))],
                [$this->equalTo(new \ReflectionClass(TestParent::class))]
            )
            ->will($this->onConsecutiveCalls(
                new ClassMetadata(TestObject::class),
                new ClassMetadata(TestParent::class)
            ));

        $factory = new MetadataFactory($driver);
        $metadata = $factory->getMetadataForClass(TestParent::class);

        $this->assertInstanceOf(ClassHierarchyMetadata::class, $metadata);
        $this->assertCount(2, $metadata->classMetadata);
    }

    public function testGetMetadataForClassWhenMergeable()
    {
        $driver = $this->createMock(DriverInterface::class);

        $driver
            ->expects($this->exactly(2))
            ->method('loadMetadataForClass')
            ->withConsecutive(
                [$this->equalTo(new \ReflectionClass(TestObject::class))],
                [$this->equalTo(new \ReflectionClass(TestParent::class))]
            )
            ->will($this->onConsecutiveCalls(
                new MergeableClassMetadata(TestObject::class),
                new MergeableClassMetadata(TestParent::class)
            ));

        $factory = new MetadataFactory($driver);
        $metadata = $factory->getMetadataForClass(TestParent::class);

        $this->assertInstanceOf(MergeableClassMetadata::class, $metadata);
        $this->assertEquals('Metadata\Tests\Fixtures\TestParent', $metadata->name);
    }

    public function testGetMetadataWithComplexHierarchy()
    {
        $driver = $this->createMock(DriverInterface::class);

        $driver
            ->expects($this->any())
            ->method('loadMetadataForClass')
            ->will($this->returnCallback(static function ($class) {
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
            }));

        $factory = new MetadataFactory($driver);

        $subClassA = $factory->getMetadataForClass(SubClassA::class);
        $this->assertInstanceOf(MergeableClassMetadata::class, $subClassA);
        $this->assertEquals(['foo', 'bar'], array_keys($subClassA->propertyMetadata));

        $subClassB = $factory->getMetadataForClass('Metadata\Tests\Fixtures\ComplexHierarchy\SubClassB');
        $this->assertInstanceOf(MergeableClassMetadata::class, $subClassB);
        $this->assertEquals(['foo', 'baz'], array_keys($subClassB->propertyMetadata));
    }

    public function testGetMetadataWithCache()
    {
        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata = new ClassMetadata(TestObject::class)));

        $factory = new MetadataFactory($driver);

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo(TestObject::class))
            ->will($this->returnValue(null));
        $cache
            ->expects($this->once())
            ->method('put')
            ->with($this->equalTo($metadata));
        $factory->setCache($cache);

        $factory->getMetadataForClass(TestObject::class);
        $factory->getMetadataForClass(TestObject::class);
        $this->assertSame($metadata, reset($factory->getMetadataForClass(TestObject::class)->classMetadata));
    }

    public function testGetMetadataReturnsNullIfNoMetadataIsFound()
    {
        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null));

        $factory = new MetadataFactory($driver);

        $this->assertNull($factory->getMetadataForClass(\stdClass::class));
    }

    public function testGetMetadataWithInterfaces()
    {
        $driver = $this->createMock(DriverInterface::class);

        $driver
            ->expects($this->exactly(4))
            ->method('loadMetadataForClass')
            ->withConsecutive(
                [$this->equalTo(new \ReflectionClass(InterfaceA::class))],
                [$this->equalTo(new \ReflectionClass(BaseClass::class))],
                [$this->equalTo(new \ReflectionClass(InterfaceB::class))],
                [$this->equalTo(new \ReflectionClass(SubClassA::class))]
            );

        $factory = new MetadataFactory($driver);
        $factory->setIncludeInterfaces(true);

        $factory->getMetadataForClass(SubClassA::class);
    }

    public function testGetAllClassNames()
    {
        $driver = $this->createMock(AdvancedDriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('getAllClassNames')
            ->will($this->returnValue([]));

        $factory = new MetadataFactory($driver);
        $this->assertSame([], $factory->getAllClassNames());
    }

    public function testGetAllClassNamesThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $factory = new MetadataFactory($this->createMock(DriverInterface::class));
        $factory->getAllClassNames();
    }

    public function testNotFoundMetadataIsCached()
    {
        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once()) // This is the important part of this test
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null));

        $cachedMetadata = null;
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo(TestObject::class))
            ->will($this->returnCallback(static function () use (&$cachedMetadata) {
                return $cachedMetadata;
            }));
        $cache
            ->expects($this->once())
            ->method('put')
            ->will($this->returnCallback(static function ($metadata) use (&$cachedMetadata) {
                $cachedMetadata = $metadata;
            }));

        $factory = new MetadataFactory($driver);
        $factory->setCache($cache);
        $factory->getMetadataForClass(TestObject::class);
        $factory->getMetadataForClass(TestObject::class);
        $this->assertNull($factory->getMetadataForClass(TestObject::class));

        // We use another factory with the same cache, to simulate another request and skip the in memory
        $factory = new MetadataFactory($driver);
        $factory->setCache($cache);
        $factory->getMetadataForClass(TestObject::class);
        $factory->getMetadataForClass(TestObject::class);
        $this->assertNull($factory->getMetadataForClass(TestObject::class));
    }

    public function testNotFoundMetadataIsNotCachedInDebug()
    {
        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->exactly(2))
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null));

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->any())
            ->method('load')
            ->with($this->equalTo(TestObject::class))
            ->will($this->returnValue(null));
        $cache
            ->expects($this->never())
            ->method('put');

        $factory = new MetadataFactory($driver, ClassHierarchyMetadata::class, true);
        $factory->setCache($cache);
        $factory->getMetadataForClass(TestObject::class);
        $this->assertNull($factory->getMetadataForClass(TestObject::class));

        // We use another factory with the same cache, to simulate another request and skip the in memory
        $factory = new MetadataFactory($driver, ClassHierarchyMetadata::class, true);
        $factory->setCache($cache);
        $factory->getMetadataForClass(TestObject::class);
        $this->assertNull($factory->getMetadataForClass(TestObject::class));
    }
}
