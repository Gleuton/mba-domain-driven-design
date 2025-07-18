<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Models\EventSpotModel;

class EventSpotMapper
{
    public static function toModel(EventSpot $spot): EventSpotModel
    {
        $spotArray = $spot->toArray();

        return new EventSpotModel([
            'id' => $spotArray['id'],
            'location' => $spotArray['location'] ?? null,
            'is_reserved' => $spotArray['is_reserved'],
            'is_published' => $spotArray['is_published'],
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