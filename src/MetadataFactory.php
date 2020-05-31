<?php

declare(strict_types=1);

namespace Metadata;

use Metadata\Cache\CacheInterface;
use Metadata\Driver\AdvancedDriverInterface;
use Metadata\Driver\DriverInterface;

class MetadataFactory implements AdvancedMetadataFactoryInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ClassMetadata[]
     */
    private $loadedMetadata = [];

    /**
     * @var ClassMetadata[]
     */
    private $loadedClassMetadata = [];

    /**
     * @var null|string
     */
    private $hierarchyMetadataClass;

    /**
     * @var bool
     */
    private $includeInterfaces = false;

    /**
     * @var bool
     */
    private $debug = false;

    public function __construct(DriverInterface $driver, ?string $hierarchyMetadataClass = 'Metadata\ClassHierarchyMetadata', bool $debug = false)
    {
        $this->driver = $driver;
        $this->hierarchyMetadataClass = $hierarchyMetadataClass;
        $this->debug = $debug;
    }

    public function setIncludeInterfaces(bool $include): void
    {
        $this->includeInterfaces = $include;
    }

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }


    /**
     * {@inheritDoc}
     */
    public function getMetadataForClass(string $className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->filterNullMetadata($this->loadedMetadata[$className]);
        }

        $metadata = null;
        foreach ($this->getClassHierarchy($className) as $class) {
            if (isset($this->loadedClassMetadata[$name = $class->getName()])) {
                if (null !== $classMetadata = $this->filterNullMetadata($this->loadedClassMetadata[$name])) {
                    $this->addClassMetadata($metadata, $classMetadata);
                }
                continue;
            }

            // check the cache
            if (null !== $this->cache) {
                if (($classMetadata = $this->cache->load($class->getName())) instanceof NullMetadata) {
                    $this->loadedClassMetadata[$name] = $classMetadata;
                    continue;
                }

                if (null !== $classMetadata) {
                    if (!$classMetadata instanceof ClassMetadata) {
                        throw new \LogicException(sprintf(
                            'The cache must return instances of ClassMetadata for class %s, but got %s.',
                            $className,
                            var_export($classMetadata, true)
                        ));
                    }

                    if ($this->debug && !$classMetadata->isFresh()) {
                        $this->cache->evict($classMetadata->name);
                    } else {
                        $this->loadedClassMetadata[$name] = $classMetadata;
                        $this->addClassMetadata($metadata, $classMetadata);
                        continue;
                    }
                }
            }

            // load from source
            if (null !== $classMetadata = $this->driver->loadMetadataForClass($class)) {
                $this->loadedClassMetadata[$name] = $classMetadata;
                $this->addClassMetadata($metadata, $classMetadata);

                if (null !== $this->cache) {
                    $this->cache->put($classMetadata);
                }

                continue;
            }

            if (null !== $this->cache && !$this->debug) {
                $this->cache->put(new NullMetadata($class->getName()));
            }
        }

        if (null === $metadata) {
            $metadata = new NullMetadata($className);
        }

        return $this->filterNullMetadata($this->loadedMetadata[$className] = $metadata);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames(): array
    {
        if (!$this->driver instanceof AdvancedDriverInterface) {
            throw new \RuntimeException(
                sprintf('Driver "%s" must be an instance of "AdvancedDriverInterface".', get_class($this->driver))
            );
        }

        return $this->driver->getAllClassNames();
    }

    /**
     * @param MergeableInterface|ClassHierarchyMetadata $metadata
     */
    private function addClassMetadata(&$metadata, ClassMetadata $toAdd): void
    {
        if ($toAdd instanceof MergeableInterface) {
            if (null === $metadata) {
                $metadata = clone $toAdd;
            } else {
                $metadata->merge($toAdd);
            }
        } else {
            if (null === $metadata) {
                $class = $this->hierarchyMetadataClass;
                $metadata = new $class();
            }

            $metadata->addClassMetadata($toAdd);
        }
    }

    /**
     * @return \ReflectionClass[]
     */
    private function getClassHierarchy(string $class): array
    {
        $classes = [];
        $refl = new \ReflectionClass($class);

        do {
            $classes[] = $refl;
            $refl = $refl->getParentClass();
        } while (false !== $refl);

        $classes = array_reverse($classes, false);

        if (!$this->includeInterfaces) {
            return $classes;
        }

        $addedInterfaces = [];
        $newHierarchy = [];

        foreach ($classes as $class) {
            foreach ($class->getInterfaces() as $interface) {
                if (isset($addedInterfaces[$interface->getName()])) {
                    continue;
                }
                $addedInterfaces[$interface->getName()] = true;

                $newHierarchy[] = $interface;
            }

            $newHierarchy[] = $class;
        }

        return $newHierarchy;
    }

    /**
     * @param ClassMetadata|ClassHierarchyMetadata|MergeableInterface $metadata
     * @return ClassMetadata|ClassHierarchyMetadata|MergeableInterface
     */
    private function filterNullMetadata($metadata = null)
    {
        return !$metadata instanceof NullMetadata ? $metadata : null;
    }
}
