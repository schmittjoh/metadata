<?php

namespace Metadata\Tests\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverChain;

class DriverChainTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadMetadataForClass()
    {
        $driver = $this->getMock('Metadata\\Driver\\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue($metadata = new ClassMetadata('\stdClass')))
        ;
        $chain = new DriverChain(array($driver));

        $this->assertSame($metadata, $chain->loadMetadataForClass(new \ReflectionClass('\stdClass')));
    }

    public function testLoadMetadataForClassReturnsNullWhenNoMetadataIsFound()
    {
        $driver = new DriverChain(array());
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass('\stdClass')));

        $driver = $this->getMock('Metadata\\Driver\\DriverInterface');
        $driver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->will($this->returnValue(null))
        ;
        $driverChain = new DriverChain(array($driver));
        $this->assertNull($driver->loadMetadataForClass(new \ReflectionClass('\stdClass')));
    }
}
