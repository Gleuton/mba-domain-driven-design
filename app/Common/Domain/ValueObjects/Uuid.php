<?php

namespace App\Common\Domain\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid extends ValueObject
{
    public function __construct(private ?string $uuid = null)
    {
        $uuid = $uuid ?? RamseyUuid::uuid4()->toString();

        if (!RamseyUuid::isValid($uuid)) {
            throw new InvalidArgumentException("UUID inválido: $uuid");
        }

        $this->uuid = $uuid;
    }

    protected function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
        ];
    }

    public function getValue(): string
    {
        return $this->uuid;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->uuid === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->uuid;
    }
}