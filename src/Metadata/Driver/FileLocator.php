<?php

namespace Metadata\Driver;

class FileLocator implements FileLocatorInterface
{
    private $dirs;

    public function __construct(array $dirs)
    {
        $this->dirs = $dirs;
    }

    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * @param string $extension
     */
    public function findFileForClass(\ReflectionClass $class, $extension)
    {
        foreach ($this->dirs as $prefix => $dir) {
            if ('' !== $prefix && 0 !== strpos($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $len = '' === $prefix ? 0 : strlen($prefix) + 1;
            $path = $dir.'/'.str_replace('\\', '.', substr($class->name, $len)).'.'.$extension;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
