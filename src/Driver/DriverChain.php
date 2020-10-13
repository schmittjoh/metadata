<?php

declare(strict_types=1);

namespace Metadata\Driver;

use Metadata\ClassMetadata;

final class DriverChain implements AdvancedDriverInterface
{
    /**
     * @var DriverInterface[]
     */
    private $drivers;

    /**
     * @param DriverInterface[] $drivers
     */
    public function __construct(array $drivers = [])
    {
        $this->drivers = $drivers;
    }

    public function addDriver(DriverInterface $driver): void
    {
        $this->drivers[] = $driver;
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
        foreach ($this->drivers as $driver) {
            if (null !== $metadata = $driver->loadMetadataForClass($class)) {
                return $metadata;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames(): array
    {
        $classes = [];
        foreach ($this->drivers as $driver) {
            if (!$driver instanceof AdvancedDriverInterface) {
                throw new \RuntimeException(
                    sprintf(
                        'Driver "%s" must be an instance of "AdvancedDriverInterface" to use ' .
                        '"DriverChain::getAllClassNames()".',
                        get_class($driver)
                    )
                );
            }

            $driverClasses = $driver->getAllClassNames();
            if (!empty($driverClasses)) {
                $classes = array_merge($classes, $driverClasses);
            }
        }

        return $classes;
    }
}
