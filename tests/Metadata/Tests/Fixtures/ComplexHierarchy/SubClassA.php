<?php

namespace Metadata\Tests\Fixtures\ComplexHierarchy;

class SubClassA extends BaseClass implements InterfaceA, InterfaceB
{
    private $bar;
}