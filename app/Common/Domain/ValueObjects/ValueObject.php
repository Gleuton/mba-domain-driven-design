<?php
declare(strict_types=1);

namespace App\Common\Domain\ValueObjects;

use JsonException;
use JsonSerializable;

abstract class ValueObject implements JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    abstract protected function toArray(): array;

    public function equals(ValueObject $other): bool
    {
        return
            static::class === get_class($other)
            && $this->toArray() === $other->toArray();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
