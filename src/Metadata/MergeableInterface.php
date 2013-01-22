<?php

namespace Metadata;

interface MergeableInterface
{
    /**
     * @param MergeableInterface $object
     *
     * @return void
     */
    public function merge(MergeableInterface $object);
}
