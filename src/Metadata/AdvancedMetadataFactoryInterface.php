<?php

namespace Metadata;

/**
 * Interface for advanced Metadata Factory implementations.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Jordan Stout <j@jrdn.org>
 */
interface AdvancedMetadataFactoryInterface extends MetadataFactoryInterface
{
    /**
     * Gets all the possible classes.
     *
     * @throws \RuntimeException if driver does not an advanced driver.
     * @return array
     */
    public function getAllClassNames();
}
