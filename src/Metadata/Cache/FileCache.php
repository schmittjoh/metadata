<?php

namespace Metadata\Cache;

use Metadata\ClassMetadata;

class FileCache implements CacheInterface
{
    private $dir;

    public function __construct($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }
        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $dir));
        }

        $this->dir = rtrim($dir, '\\/');
    }

    /**
     * {@inheritDoc}
     */
    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->name, '\\', '-').'.cache.php';
        if (!file_exists($path)) {
            return null;
        }

        return include $path;
    }

    /**
     * {@inheritDoc}
     */
    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $path = $this->dir.'/'.strtr($metadata->name, '\\', '-').'.cache.php';

        $tmpFile = tempnam($this->dir, 'metadata-cache');
        file_put_contents($tmpFile, '<?php return unserialize('.var_export(serialize($metadata), true).');');
        chmod($tmpFile, 0666 & ~umask());

        if (false === @rename($tmpFile, $path)) {
            throw new \RuntimeException(sprintf('Could not write new cache file to %s.', $path));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->name, '\\', '-').'.cache.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
