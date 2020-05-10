<?php

declare(strict_types=1);

namespace Metadata\Cache;

use Metadata\ClassMetadata;

class FileCache implements CacheInterface
{
    /**
     * @var string
     */
    private $dir;

    public function __construct(string $dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        $this->dir = rtrim($dir, '\\/');
    }

    /**
     * {@inheritDoc}
     */
    public function load(string $class): ?ClassMetadata
    {
        $path = $this->dir . '/' . $this->sanitizeCacheKey($class) . '.cache.php';
        if (!file_exists($path)) {
            return null;
        }

        try {
            $metadata = include $path;
            if ($metadata instanceof ClassMetadata) {
                return $metadata;
            }
            // if the file does not return anything, the return value is integer `1`.
        } catch (\ParseError $e) {
            // ignore corrupted cache
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function put(ClassMetadata $metadata): void
    {
        if (!is_writable($this->dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $this->dir));
        }

        $path = $this->dir . '/' . $this->sanitizeCacheKey($metadata->name) . '.cache.php';

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
            $this->evict($metadata->name); // also evict the cache to not use an outdated version.

            return;
        }

        // Let's not break filesystems which do not support chmod.
        @chmod($tmpFile, 0666 & ~umask());

        $this->renameFile($tmpFile, $path);
    }

    /**
     * Renames a file with fallback for windows
     *
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

    /**
     * {@inheritDoc}
     */
    public function evict(string $class): void
    {
        $path = $this->dir . '/' . $this->sanitizeCacheKey($class) . '.cache.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * If anonymous class is to be cached, it contains invalid path characters that need to be removed/replaced
     * Example of anonymous class name: class@anonymous\x00/app/src/Controller/DefaultController.php0x7f82a7e026ec
     *
     * @param string $key
     * @return string
     */
    private function sanitizeCacheKey(string $key): string
    {
        return strtr($key, ['\\' => '-', "\0" => '', '@' => '-', '/' => '-', '.' => '-']);
    }
}
