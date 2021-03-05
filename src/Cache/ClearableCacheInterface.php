<?php

declare(strict_types=1);

namespace Metadata\Cache;

/**
 * @author Alexander Strizhak <gam6itko@gmail.com>
 */
interface ClearableCacheInterface
{
    /**
     * Clear all classes metadata from the cache.
     */
    public function clear(): bool;
}
