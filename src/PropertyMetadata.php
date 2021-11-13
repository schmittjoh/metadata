<?php

declare(strict_types=1);

namespace Metadata;

/**
 * Base class for property metadata.
 *
 * This class is intended to be extended to add your application specific
 * properties, and flags.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PropertyMetadata
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $name;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    /**
     * @return array<string>
     */
    public function __serialize(): array
    {
        return [
            $this->class,
            $this->name,
        ];
    }

    /**
     * @param array<string> $data
     */
    public function __unserialize(array $data): void
    {
        [$this->class, $this->name] = $data;
    }
}
