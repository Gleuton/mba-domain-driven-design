<?php

namespace App\Events\Infra\Mappers;

use App\Events\Domain\Entities\EventSection\EventSection;
use App\Models\EventSectionModel;

class EventSectionMapper
{
    public static function toModel(EventSection $section): EventSectionModel
    {
        $sectionArray = $section->toArray();

        return new EventSectionModel([
            'id' => $sectionArray['id'] ?? null,
            'name' => $sectionArray['name'],
            'description' => $sectionArray['description'] ?? null,
            'is_published' => $sectionArray['isPublished'],
            'total_spots' => $sectionArray['totalSpots'],
            'total_spots_reserved' => $sectionArray['totalSpotsReserved'],
            'price' => $sectionArray['price'],
            'event_id' => $sectionArray['eventId'],
        ]);
    }

    public static function toDomain(EventSectionModel $model): EventSection
    {
        return EventSection::create([
            'id' => $model->id,
            'name' => $model->name,
            'description' => $model->description,
            'isPublished' => $model->is_published,
            'totalSpots' => $model->total_spots,
            'totalSpotsReserved' => $model->total_spots_reserved,
            'price' => $model->price,
        ]);
    }
}