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
class PropertyMetadata implements \Serializable
{
    use SerializationHelper;

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

    protected function serializeToArray(): array
    {
        return [
            $this->class,
            $this->name,
        ];
    }

    protected function unserializeFromArray(array $data): void
    {
        [$this->class, $this->name] = $data;
    }
}
