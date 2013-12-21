<?php

namespace Metadata\Driver;

/**
 * Base file driver implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class AbstractFileDriver implements AdvancedDriverInterface
{
    /**
     * @var FileLocatorInterface|FileLocator
     */
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
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        if (!$this->locator instanceof AdvancedFileLocatorInterface) {
            throw new \RuntimeException('Locator "%s" must be an instance of "AdvancedFileLocatorInterface".');
        }

        return $this->locator->findAllClasses($this->getExtension());
    }

    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param \ReflectionClass $class
     * @param string           $file
     *
     * @return \Metadata\ClassMetadata|null
     */
    abstract protected function loadMetadataFromFile(\ReflectionClass $class, $file);

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    abstract protected function getExtension();
}
