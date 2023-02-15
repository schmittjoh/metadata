<?php

declare(strict_types=1);

namespace Metadata\Tests\Driver;

use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\LazyLoadingDriver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;

class LazyLoadingDriverTest extends TestCase
{
    /**
     * @var ClassMetadata|MockObject
     */
    private $metadata;

    /**
     * @var \ReflectionClass
     */
    private $ref;

    /**
     * @var DriverInterface|MockObject
     */
    private $realDriver;

    protected function setUp(): void
    {
        $this->metadata = $this->createMock(ClassMetadata::class);
        $this->ref = new \ReflectionClass(\stdClass::class);

        $this->realDriver = $this->createMock(DriverInterface::class);
    }

    public function testSymfonyContainer()
    {
        $this->realDriver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->with($this->ref)
            ->willReturn($this->metadata);

        $container = new Container();
        $container->set('foo', $this->realDriver);

        $driver = new LazyLoadingDriver($container, 'foo');

        self::assertSame($this->metadata, $driver->loadMetadataForClass($this->ref));
    }

    public function testPsrContainer()
    {
        $this->realDriver
            ->expects($this->once())
            ->method('loadMetadataForClass')
            ->with($this->ref)
            ->willReturn($this->metadata);

        $container = new class ($this->realDriver) implements ContainerInterface {
            private $service;

            public function __construct($service)
            {
                $this->service = $service;
            }

            public function get($id)
            {
                return $this->service;
            }

            public function has($id): bool
            {
                return true;
            }
        };
        $driver = new LazyLoadingDriver($container, 'foo');

        self::assertSame($this->metadata, $driver->loadMetadataForClass($this->ref));
    }

    public function testWrongContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        new LazyLoadingDriver(new \stdClass(), 'foo');
    }
}
