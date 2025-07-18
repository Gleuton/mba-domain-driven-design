<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\Event\Event;
use App\Models\EventModel;

class EventMapper
{
    public static function toModel(Event $event): EventModel
    {
        $eventArray = $event->toArray();

        return new EventModel([
            'id' => $eventArray['id'],
            'name' => $eventArray['name'],
            'description' => $eventArray['description'] ?? null,
            'date' => $eventArray['date'],
            'is_published' => $eventArray['is_published'],
            'total_spots' => $eventArray['total_spots'],
            'total_spots_reserved' => $eventArray['total_spots_reserved'],
            'partner_id' => $eventArray['partner_id'],
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