<?php

declare(strict_types=1);

namespace Metadata;

interface MergeableInterface
{
    public function merge(MergeableInterface $object): void;
}
