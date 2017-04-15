<?php

namespace Metadata\Driver;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingDriver implements DriverInterface
{
    private $container;
    private $realDriverId;

    public function __construct(ContainerInterface $container, $realDriverId)
    {
        $this->container = $container;
        $this->realDriverId = $realDriverId;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        return $this->container->get($this->realDriverId)->loadMetadataForClass($class);
    }
}
