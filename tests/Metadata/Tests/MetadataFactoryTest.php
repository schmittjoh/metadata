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
                return new ClassMetadata($class);
            }))
        ;
        $driver
            ->expects($this->at(1))
            ->method('loadMetadataForClass')
            ->with($this->equalTo(new \ReflectionClass('Metadata\Tests\Fixtures\TestParent')))
            ->will($this->returnCallback(function($class) {
                return new ClassMetadata($class);
            }))
        ;

        $factory = new MetadataFactory($driver);
        $metadata = $factory->getMetadataForClass('Metadata\Tests\Fixtures\TestParent');

        $this->assertInstanceOf('Metadata\ClassHierarchyMetadata', $metadata);
        $this->assertEquals(2, count($metadata->getClassMetadata()));
    }
}