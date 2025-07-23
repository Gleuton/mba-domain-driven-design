<?php


declare(strict_types=1);

namespace App\Events\Domain\Entities\EventSpot;

use App\Common\Domain\AbstractCollection;
use App\Common\Domain\AbstractEntity;

class EventSpotCollection extends AbstractCollection
{
    public function validate(AbstractEntity $entity): void
    {
        if (!$entity instanceof EventSpot) {
            throw new \InvalidArgumentException('The entity must be an instance of EventSpot');
        }
    }

    public function spotById(EventSpotId $eventSpotId): EventSpot
    {
        $spot = $this->find(
            fn(EventSpot $spot) => $spot->equals($eventSpotId)
        );

        if (!$spot instanceof EventSpot) {
            throw new \InvalidArgumentException("Spot not found for ID: {$eventSpotId}");
        }

        return $spot;
    }
}