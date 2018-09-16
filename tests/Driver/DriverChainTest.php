<?php

declare(strict_types=1);

namespace Metadata\Tests\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverChain;
use PHPUnit\Framework\TestCase;

class DriverChainTest extends TestCase
{
    public function testLoadMetadataForClass()
    {
        $driver = $this->createMock('Metadata\\Driver\\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata = new ClassMetadata('\stdClass')))
        ;
        $chain = new DriverChain([$driver]);

        $this->assertSame($metadata, $chain->loadMetadataForClass(new \ReflectionClass('\stdClass')));
    }

    public function testGetAllClassNames()
    {
        $driver1 = $this->createMock('Metadata\\Driver\\AdvancedDriverInterface');
        $driver1
            ->expects($this->once())
            ->method('getAllClassNames')
            ->will($this->returnValue(['Foo']));

        $driver2 = $this->createMock('Metadata\\Driver\\AdvancedDriverInterface');
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
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass('\stdClass')));

        $driver = $this->createMock('Metadata\\Driver\\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;
        $driverChain = new DriverChain([$driver]);
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass('\stdClass')));
    }

    public function testGetAllClassNamesThrowsException()
    {
        $this->expectException('RuntimeException');
        $driver = $this->createMock('Metadata\\Driver\\DriverInterface');
        $chain = new DriverChain([$driver]);
        $chain->getAllClassNames();
    }
}
