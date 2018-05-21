<?php

namespace Metadata\Driver;

/**
 * Forces advanced logic to drivers.
 *
 * @author Jordan Stout <j@jrdn.org>
 */
interface AdvancedDriverInterface extends DriverInterface
{
    /**
     * Gets all the metadata class names known to this driver.
     *
     * @return array
     */
    public function getAllClassNames();
}
