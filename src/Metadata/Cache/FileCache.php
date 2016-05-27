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
        
        // Let's not break filesystems which do not support chmod.
        @chmod($tmpFile, 0666 & ~umask());

        $this->renameFile($tmpFile, $path);
    }

    /**
     * Renames a file with fallback for windows
     *
     * @param string $source
     * @param string $target
     */
    private function renameFile($source, $target) {
        if (false === @rename($source, $target)) {
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                if (false === copy($source, $target)) {
                    throw new \RuntimeException(sprintf('(WIN) Could not write new cache file to %s.', $target));
                }
                if (false === unlink($source)) {
                    throw new \RuntimeException(sprintf('(WIN) Could not delete temp cache file to %s.', $source));
                }
            } else {
                throw new \RuntimeException(sprintf('Could not write new cache file to %s.', $target));
            }
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
