<?php

namespace Metadata;

interface MergeableInterface
{
    function merge(MergeableInterface $object);
}