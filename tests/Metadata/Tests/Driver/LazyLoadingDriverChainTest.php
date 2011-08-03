<?php

namespace Metadata\Tests\Driver;

use Metadata\Driver\LazyLoadingDriverChain;
use Metadata\ClassMetadata;

class LazyLoadingDriverChainTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadMetadataFromClass()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $self = $this;
        $driverIds = array('a', 'b', 'c', 'd', 'e');
        for ($i=0,$c=count($driverIds); $i<$c; $i++) {
            $container
                ->expects($this->at($i))
                ->method('get')
                ->with($this->equalTo($driverIds[$i]))
                ->will($this->returnCallback(function($v) use ($self, $i) {
                    $mock = $self->getMock('Metadata\Driver\DriverInterface');

                    if ($i < 2) {
                        $mock
                            ->expects($self->once())
                            ->method('loadMetadataForClass')
                            ->will($self->returnValue(null))
                        ;
                    } else if ($i == 2) {
                        $mock
                            ->expects($self->once())
                            ->method('loadMetadataForClass')
                            ->will($self->returnValue(new ClassMetadata('stdClass')))
                        ;
                    } else {
                        $mock
                            ->expects($self->never())
                            ->method('loadMetadataForClass')
                        ;
                    }

                    return $mock;
                }))
            ;
        }

        $driver = new LazyLoadingDriverChain($container, $driverIds);
        $this->assertNotNull($driver->loadMetadataForClass(new \ReflectionClass('stdClass')));
    }
}