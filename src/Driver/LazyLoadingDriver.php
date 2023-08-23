<?php

declare(strict_types=1);

namespace Metadata\Driver;

use Metadata\ClassMetadata;
use Psr\Container\ContainerInterface;

class LazyLoadingDriver implements DriverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $realDriverId;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container, string $realDriverId)
    {
        if (!$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException(sprintf('The container must be an instance of %s (%s given).', ContainerInterface::class, \is_object($container) ? \get_class($container) : \gettype($container)));
        }

        $this->container = $container;
        $this->realDriverId = $realDriverId;
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
        return $this->container->get($this->realDriverId)->loadMetadataForClass($class);
    }
}
