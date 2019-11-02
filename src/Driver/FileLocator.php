<?php

declare(strict_types=1);

namespace Metadata\Driver;

use function file_exists;
use function str_replace;
use function strpos;
use function substr;

class FileLocator implements AdvancedFileLocatorInterface
{
    /**
     * @var string[]
     */
    private $dirs;

    /**
     * @param string[] $dirs
     */
    public function __construct(array $dirs)
    {
        $this->dirs = $dirs;
    }

    public function findFileForClass(\ReflectionClass $class, string $extension): ?string
    {
        foreach ($this->dirs as $prefix => $dir) {
            if ('' !== $prefix && 0 !== strpos($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $len = '' === $prefix ? 0 : strlen($prefix) + 1;
            $fqcn = str_replace('\\', '.', substr($class->name, $len));

            while (true) {
                $path = $dir . '/' . $fqcn . '.' . $extension;
                if (file_exists($path)) {
                    return $path;
                }

                if (false === strpos($fqcn, '.')) {
                    break;
                }

                $fqcn = substr($fqcn, 0, strrpos($fqcn, '.'));
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function findAllClasses(string $extension): array
    {
        $classes = [];
        foreach ($this->dirs as $prefix => $dir) {
            /** @var \RecursiveIteratorIterator|\SplFileInfo[] $iterator */
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            $nsPrefix = '' !== $prefix ? $prefix . '\\' : '';
            foreach ($iterator as $file) {
                if (($fileName = $file->getBasename('.' . $extension)) === $file->getBasename()) {
                    continue;
                }

                $classes[] = $nsPrefix . str_replace('.', '\\', $fileName);
            }
        }

        return $classes;
    }
}
