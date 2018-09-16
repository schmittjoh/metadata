<?php

declare(strict_types=1);

namespace Metadata\Tests\Driver;

use Metadata\ClassMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @author Jordan Stout <j@jrdn.org>
 */
class AbstractFileDriverTest extends TestCase
{
    private static $extension = 'jms_metadata.yml';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $locator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $driver;

    public function setUp()
    {
        $this->locator = $this->createMock('Metadata\Driver\FileLocator', [], [], '', false);
        $this->driver = $this->getMockBuilder('Metadata\Driver\AbstractFileDriver')
            ->setConstructorArgs([$this->locator])
            ->getMockForAbstractClass();

        $this->driver->expects($this->any())->method('getExtension')->will($this->returnValue(self::$extension));
    }

    public function testLoadMetadataForClass()
    {
        $class = new \ReflectionClass('\stdClass');
        $this->locator
            ->expects($this->once())
            ->method('findFileForClass')
            ->with($class, self::$extension)
            ->will($this->returnValue('Some\Path'));

        $this->driver
            ->expects($this->once())
            ->method('loadMetadataFromFile')
            ->with($class, 'Some\Path')
            ->will($this->returnValue($metadata = new ClassMetadata('\stdClass')));

        $this->assertSame($metadata, $this->driver->loadMetadataForClass($class));
    }

    public function testLoadMetadataForClassWillReturnNull()
    {
        $class = new \ReflectionClass('\stdClass');
        $this->locator
            ->expects($this->once())
            ->method('findFileForClass')
            ->with($class, self::$extension)
            ->will($this->returnValue(null));

        $this->assertSame(null, $this->driver->loadMetadataForClass($class));
    }

    public function testGetAllClassNames()
    {
        $class = new \ReflectionClass('\stdClass');
        $this->locator
            ->expects($this->once())
            ->method('findAllClasses')
            ->with(self::$extension)
            ->will($this->returnValue(['\stdClass']));

        $this->assertSame(['\stdClass'], $this->driver->getAllClassNames($class));
    }

    public function testGetAllClassNamesThrowsRuntimeException()
    {
        $this->expectException('RuntimeException');

        $locator = $this->createMock('Metadata\Driver\FileLocatorInterface');
        $driver = $this->getMockBuilder('Metadata\Driver\AbstractFileDriver')
            ->setConstructorArgs([$locator])
            ->getMockForAbstractClass();
        $class = new \ReflectionClass('\stdClass');

        $driver->getAllClassNames($class);
    }
}
