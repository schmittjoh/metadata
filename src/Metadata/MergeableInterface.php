<?php

namespace Metadata;

interface MergeableInterface
{
    /**
     * @return void
     */
    public function merge(MergeableInterface $object);
}
