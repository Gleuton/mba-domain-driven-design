<?php

namespace App\Events\Infra\Repository;

use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventCollection;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Repositories\EventsRepositoryInterface;
use App\Events\Infra\Mapper\EventMapper;
use App\Models\EventModel;
use Exception;

class EventRepository implements EventsRepositoryInterface
{
    public function save(Event $entity): void
    {
        $entityArray = $entity->toArray();
        $model = EventModel::find($entityArray['id']) ?? EventMapper::toModel($entity);

        $model->name = $entityArray['name'];
        $model->description = $entityArray['description'];
        $model->date = $entityArray['date'];
        $model->is_published = $entityArray['is_published'];

        $model->save();
    }

    public function findById(EventId $id): Event
    {
        $partnerModel = EventModel::find($id->getValue());

        if (!$partnerModel) {
            throw new Exception("Customer not found");
        }

        return EventMapper::toDomain($partnerModel);
    }

    public function findAll(): EventCollection
    {
        $models = EventModel::all();
        $collection = new EventCollection();

        foreach ($models as $model) {
            $collection->add(EventMapper::toDomain($model));
        }

        return $collection;
    }

    public function remove(Event $entity): void
    {
        $entityArray = $entity->toArray();

        $model = EventModel::find($entityArray['id']);
        $model->delete();
    }
}