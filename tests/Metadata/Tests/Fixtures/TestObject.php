<?php

namespace Metadata\Tests\Fixtures;

class TestObject
{
    private $foo;

    public function getFoo()
    {
        return $this->foo;
    }

    private function setFoo($foo)
    {
        $this->foo = $foo;
    }
}
