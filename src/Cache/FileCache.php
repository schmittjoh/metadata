<?php

declare(strict_types=1);

namespace Metadata\Cache;

use Metadata\ClassMetadata;

class FileCache implements CacheInterface, ClearableCacheInterface
{
    /**
     * @var string
     */
    private $dir;

    public function __construct(string $dir)
    {
        if (!is_dir($dir) && false === @mkdir($dir, 0777, true)) {
            throw new \InvalidArgumentException(sprintf('Can\'t create directory for cache at "%s"', $dir));
        }

        $this->dir = rtrim($dir, '\\/');
    }

    public function load(string $class): ?ClassMetadata
    {
        $path = $this->getCachePath($class);
        if (!is_readable($path)) {
            return null;
        }

        try {
            $metadata = include $path;
            if ($metadata instanceof ClassMetadata) {
                return $metadata;
            }

            // if the file does not return anything, the return value is integer `1`.
        } catch (\Error $e) {
            // ignore corrupted cache
        }

        return null;
    }

    public function put(ClassMetadata $metadata): void
    {
        if (!is_writable($this->dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $this->dir));
        }

        $path = $this->getCachePath($metadata->name);
        if (!is_writable(dirname($path))) {
            throw new \RuntimeException(sprintf('Cache file "%s" is not writable.', $path));
        }

        $tmpFile = tempnam($this->dir, 'metadata-cache');
        if (false === $tmpFile) {
            $this->evict($metadata->name);

            return;
        }

        $data = '<?php return unserialize(' . var_export(serialize($metadata), true) . ');';
        $bytesWritten = file_put_contents($tmpFile, $data);
        // use strlen and not mb_strlen. if there is utf8 in the code, it also writes more bytes.
        if ($bytesWritten !== strlen($data)) {
            @unlink($tmpFile);
            // also evict the cache to not use an outdated version.
            $this->evict($metadata->name);

            return;
        }

        // Let's not break filesystems which do not support chmod.
        @chmod($tmpFile, 0666 & ~umask());

        $this->renameFile($tmpFile, $path);
    }

    /**
     * Renames a file with fallback for windows
     */
    private function renameFile(string $source, string $target): void
    {
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

    public function evict(string $class): void
    {
        $path = $this->getCachePath($class);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function clear(): bool
    {
        $result = true;
        $files = glob($this->dir . '/*.cache.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                $result = $result && @unlink($file);
            }
        }

        return $result;
    }

    /**
     * This function computes the cache file path.
     *
     * If anonymous class is to be cached, it contains invalid path characters that need to be removed/replaced
     * Example of anonymous class name: class@anonymous\x00/app/src/Controller/DefaultController.php0x7f82a7e026ec
     */
    private function getCachePath(string $key): string
    {
        $fileName = str_replace(['\\', "\0", '@', '/', '$', '{', '}', ':'], '-', $key);

        return $this->dir . '/' . $fileName . '.cache.php';
    }
}
