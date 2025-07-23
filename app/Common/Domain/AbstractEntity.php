<?php

namespace App\Common\Domain;

use App\Common\Domain\ValueObjects\Uuid;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use JsonException;
use ReflectionObject;

abstract class AbstractEntity
{
    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return json_encode($this->serializableFields(), JSON_THROW_ON_ERROR);
    }

    protected function serializableFields(): array
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties();

        $data = [];
        foreach ($properties as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }

    public function toArray(): array
    {
        return $this->serializableFields();
    }
}