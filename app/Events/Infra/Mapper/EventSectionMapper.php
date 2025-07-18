<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\EventSection\EventSection;
use App\Models\EventSectionModel;

class EventSectionMapper
{
    public static function toModel(EventSection $section): EventSectionModel
    {
        $sectionArray = $section->toArray();

        return new EventSectionModel([
            'id' => $sectionArray['id'],
            'name' => $sectionArray['name'],
            'description' => $sectionArray['description'] ?? null,
            'is_published' => $sectionArray['is_published'],
            'total_spots' => $sectionArray['total_spots'],
            'total_spots_reserved' => $sectionArray['total_spots_reserved'],
            'price' => $sectionArray['price'],
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