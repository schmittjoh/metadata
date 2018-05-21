<?php

namespace Metadata\Driver;

/**
 * Forces advanced logic on a file locator.
 *
 * @author Jordan Stout <j@jrdn.org>
 */
interface AdvancedFileLocatorInterface extends FileLocatorInterface
{
    /**
     * Finds all possible metadata files.
     *
     * @param string $extension
     *
     * @return array
     */
    public function findAllClasses($extension);
}
