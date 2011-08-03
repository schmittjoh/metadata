<?php

namespace Metadata\Driver;

/**
 * Base file driver implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class AbstractFileDriver implements DriverInterface
{
    private $locator;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (null === $path = $this->locator->findFileForClass($class, $this->getExtension())) {
            return null;
        }

        return $this->loadMetadataFromFile($class, $path);
    }

    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param string $file
     * @return ClassMetadata|null
     */
    abstract protected function loadMetadataFromFile(\ReflectionClass $class, $file);

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    abstract protected function getExtension();
}