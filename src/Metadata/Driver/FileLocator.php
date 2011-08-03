<?php

namespace Metadata\Driver;

class FileLocator implements FileLocatorInterface
{
    private $dirs;

    public function __construct(array $dirs)
    {
        $this->dirs = $dirs;
    }

    public function findFileForClass(\ReflectionClass $class, $extension)
    {
        foreach ($this->dirs as $prefix => $dir) {
            if (0 !== strpos($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $path = $dir.'/'.str_replace('\\', '.', substr($class->getName(), strlen($prefix)+1)).'.'.$extension;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}