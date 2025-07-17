<?php

namespace App\Events\Infra\Mappers;

use App\Events\Domain\Entities\Event\Event;
use App\Models\EventModel;

class EventMapper
{
    public static function toModel(Event $event): EventModel
    {
        $eventArray = $event->toArray();

        return new EventModel([
            'id' => $eventArray['id'] ?? null,
            'name' => $eventArray['name'],
            'description' => $eventArray['description'] ?? null,
            'date' => $eventArray['date'],
            'is_published' => $eventArray['isPublished'],
            'total_spots' => $eventArray['totalSpots'],
            'total_spots_reserved' => $eventArray['totalSpotsReserved'],
            'partner_id' => $eventArray['partnerId'],
        ]);
    }

    public static function toDomain(EventModel $model): Event
    {
        return Event::create([
            'id' => $model->id,
            'name' => $model->name,
            'description' => $model->description,
            'date' => $model->date,
            'partnerId' => $model->partner_id,
        ]);
    }
}