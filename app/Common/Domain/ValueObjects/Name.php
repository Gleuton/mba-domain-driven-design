<?php

namespace App\Common\Domain\ValueObjects;

use InvalidArgumentException;

class Name extends ValueObject
{
    private string $name;

    public function __construct(readonly string $value)
    {
        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('Name não pode ser vazio');
        }
        if (mb_strlen($value) > 100) {
            throw new InvalidArgumentException('Name não pode ter mais que 100 caracteres');
        }

        $this->name = $value;
    }

    public function getValue(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    protected function toArray(): array
    {
        return [$this->name];
    }
}
