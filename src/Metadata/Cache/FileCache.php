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

    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->getName(), '\\', '-').'.cache.php';
        if (!file_exists($path)) {
            return null;
        }

        return include $path;
    }

    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $path = $this->dir.'/'.strtr($metadata->name, '\\', '-').'.cache.php';
        file_put_contents($path, '<?php return unserialize('.var_export(serialize($metadata), true).');');
    }

    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->getName(), '\\', '-').'.cache.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}