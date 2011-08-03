<?php

namespace Metadata\Driver;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyLoadingDriverChain implements DriverInterface
{
    private $container;
    private $driverIds;
    private $drivers = array();

    public function __construct(ContainerInterface $container, array $driverIds)
    {
        $this->container = $container;
        $this->driverIds = $driverIds;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (!$this->drivers) {
            foreach ($this->driverIds as $id) {
                $this->drivers[] = $this->container->get($id);
            }
        }

        foreach ($this->drivers as $driver) {
            if (null !== $metadata = $driver->loadMetadataForClass($class)) {
                return $metadata;
            }
        }

        return null;
    }
}