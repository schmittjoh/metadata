<?php

namespace Metadata;

use Metadata\Driver\AdvancedDriverInterface;
use Metadata\Driver\DriverInterface;
use Metadata\Cache\CacheInterface;

class MetadataFactory implements AdvancedMetadataFactoryInterface
{
    private $driver;
    private $cache;
    private $loadedMetadata = array();
    private $loadedClassMetadata = array();
    private $hierarchyMetadataClass;
    private $includeInterfaces = false;
    private $debug;

    /**
     * @param DriverInterface $driver
     * @param string          $hierarchyMetadataClass
     * @param boolean         $debug
     */
    public function __construct(DriverInterface $driver, $hierarchyMetadataClass = 'Metadata\ClassHierarchyMetadata', $debug = false)
    {
        $this->driver = $driver;
        $this->hierarchyMetadataClass = $hierarchyMetadataClass;
        $this->debug = (Boolean) $debug;
    }

    /**
     * @param boolean $include
     */
    public function setIncludeInterfaces($include)
    {
        $this->includeInterfaces = (Boolean) $include;
    }

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $className
     *
     * @return ClassHierarchyMetadata|MergeableClassMetadata|null
     */
    public function getMetadataForClass($className)
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
                if (($classMetadata = $this->cache->loadClassMetadataFromCache($class)) instanceof NullMetadata) {
                    $this->loadedClassMetadata[$name] = $classMetadata;
                    continue;
                }

                if (null !== $classMetadata) {
                    if ( ! $classMetadata instanceof ClassMetadata) {
                        throw new \LogicException(sprintf('The cache must return instances of ClassMetadata, but got %s.', var_export($classMetadata, true)));
                    }

                    if ($this->debug && !$classMetadata->isFresh()) {
                        $this->cache->evictClassMetadataFromCache($classMetadata->reflection);
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
                    $this->cache->putClassMetadataInCache($classMetadata);
                }

                continue;
            }

            if (null !== $this->cache && !$this->debug) {
                $this->cache->putClassMetadataInCache(new NullMetadata($class->getName()));
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
    public function getAllClassNames()
    {
        if (!$this->driver instanceof AdvancedDriverInterface) {
            throw new \RuntimeException(
                sprintf('Driver "%s" must be an instance of "AdvancedDriverInterface".', get_class($this->driver))
            );
        }

        return $this->driver->getAllClassNames();
    }

    /**
     * @param ClassMetadata|null $metadata
     * @param ClassMetadata      $toAdd
     */
    private function addClassMetadata(&$metadata, $toAdd)
    {
        if ($toAdd instanceof MergeableInterface) {
            if (null === $metadata) {
                $metadata = clone $toAdd;
            } else {
                $metadata->merge($toAdd);
            }
        } else {
            if (null === $metadata) {
                $metadata = new $this->hierarchyMetadataClass;
            }

            $metadata->addClassMetadata($toAdd);
        }
    }

    /**
     * @param string $class
     */
    private function getClassHierarchy($class)
    {
        $classes = array();
        $refl = new \ReflectionClass($class);

        do {
            $classes[] = $refl;
            $refl = $refl->getParentClass();
        } while (false !== $refl);

        $classes = array_reverse($classes, false);

        if (!$this->includeInterfaces) {
            return $classes;
        }

        $addedInterfaces = array();
        $newHierarchy = array();

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
     * @param NullMetadata|null $metadata
     *
     * @return ClassMetadata|null
     */
    private function filterNullMetadata($metadata = null)
    {
        return !$metadata instanceof NullMetadata ? $metadata : null;
    }
}
