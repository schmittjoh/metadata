<?php

declare(strict_types=1);

namespace Metadata\Tests\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\AdvancedDriverInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use PHPUnit\Framework\TestCase;

class DriverChainTest extends TestCase
{
    public function testLoadMetadataForClass()
    {
        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata = new ClassMetadata(\stdClass::class)));
        $chain = new DriverChain([$driver]);

        $this->assertSame($metadata, $chain->loadMetadataForClass(new \ReflectionClass(\stdClass::class)));
    }

    public function testGetAllClassNames()
    {
        $driver1 = $this->createMock(AdvancedDriverInterface::class);
        $driver1
            ->expects($this->once())
            ->method('getAllClassNames')
            ->will($this->returnValue(['Foo']));

        $driver2 = $this->createMock(AdvancedDriverInterface::class);
        $driver2
            ->expects($this->once())
            ->method('getAllClassNames')
            ->will($this->returnValue(['Bar']));

        $chain = new DriverChain([$driver1, $driver2]);

        $this->assertSame(['Foo', 'Bar'], $chain->getAllClassNames());
    }

    public function testLoadMetadataForClassReturnsNullWhenNoMetadataIsFound()
    {
        $driver = new DriverChain([]);
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass(\stdClass::class)));

        $driver = $this->createMock(DriverInterface::class);
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null));
        new DriverChain([$driver]);
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass(\stdClass::class)));
    }

    public function testGetAllClassNamesThrowsException()
    {
        $this->expectException(\RuntimeException::class);
        $driver = $this->createMock(DriverInterface::class);
        $chain = new DriverChain([$driver]);
        $chain->getAllClassNames();
    }
}
