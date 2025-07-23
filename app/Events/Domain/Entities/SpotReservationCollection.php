<?php

namespace App\Events\Domain\Entities;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

class SpotReservationCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity): void
    {
        if (!$entity instanceof SpotReservation) {
            throw new \InvalidArgumentException('The entity must be an instance of SpotReservation');
        }
    }
}