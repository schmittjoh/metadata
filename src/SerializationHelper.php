<?php

declare(strict_types=1);

namespace Metadata;

trait SerializationHelper
{
    /**
     * @deprecated Use serializeToArray
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->serializeToArray());
    }

    /**
     * @deprecated Use unserializeFromArray
     *
     * @param string $str
     *
     * @return void
     */
    public function unserialize($str)
    {
        $this->unserializeFromArray(unserialize($str));
    }

    public function __serialize(): array
    {
        return [$this->serialize()];
    }

    public function __unserialize(array $data): void
    {
        $this->unserialize($data[0]);
    }
}
