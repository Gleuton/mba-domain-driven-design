<?php

namespace App\Events\Infra\Mappers;

use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Models\EventSpotModel;

class EventSpotMapper
{
    public static function toModel(EventSpot $spot): EventSpotModel
    {
        $spotArray = $spot->toArray();

        return new EventSpotModel([
            'id' => $spotArray['id'] ?? null,
            'location' => $spotArray['location'] ?? null,
            'is_reserved' => $spotArray['isReserved'],
            'is_published' => $spotArray['isPublished'],
            'event_section_id' => $spotArray['eventSectionId'],
        ]);
    }

    public static function toDomain(EventSpotModel $model): EventSpot
    {
        return EventSpot::create([
            'spotId' => $model->id,
            'location' => $model->location,
            'isReserved' => $model->is_reserved,
            'isPublished' => $model->is_published,
        ]);
    }
}