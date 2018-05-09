<?php

namespace Metadata\Driver;

use Metadata\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingDriver implements DriverInterface
{
    private $container;
    private $realDriverId;

    public function __construct(ContainerInterface $container, string $realDriverId)
    {
        $this->container = $container;
        $this->realDriverId = $realDriverId;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
        return $this->container->get($this->realDriverId)->loadMetadataForClass($class);
    }
}
