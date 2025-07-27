<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Uuid;
use App\Common\Infra\RepositoryInterface;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventCollection;
use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Infra\Mapper\EventMapper;
use App\Events\Infra\Mapper\EventSectionMapper;
use App\Events\Infra\Mapper\EventSpotMapper;
use App\Models\EventModel;
use App\Models\EventSectionModel;
use App\Models\EventSpotModel;
use Exception;
use RuntimeException;

class EventRepository implements RepositoryInterface
{
    public function save(AbstractEntity $entity): void
    {
        if (!$entity instanceof Event) {
            throw new RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();
        $model = EventModel::find($entityArray['id']) ?? EventMapper::toModel($entity);

        $model->fill([
            'id' => $entityArray['id'] ?? null,
            'name' => $entityArray['name'] ?? null,
            'description' => $entityArray['description'] ?? null,
            'date' => $entityArray['date'] ?? null,
            'is_published' => $entityArray['is_published'] ?? false,
            'partner_id' => $entityArray['partner_id'] ?? null,
            'total_spots' => $entityArray['total_spots'] ?? 0,
            'total_spots_reserved' => $entityArray['total_spots_reserved'] ?? 0,
        ]);

        $model->save();

        $this->saveSections($entity, $model);
    }

    public function findById(Uuid $id): Event
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

    public function remove(AbstractEntity $entity): void
    {
        if (!$entity instanceof Event) {
            throw new RuntimeException("Invalid entity type");
        }

        $entityArray = $entity->toArray();

        $model = EventModel::find($entityArray['id']);
        $model->delete();
    }

    public function sectionById(EventSectionId $id): EventSection
    {
        $model = EventSectionModel::find($id->getValue());
        if (!$model) {
            throw new RuntimeException("Event section not found");
        }
        return EventSectionMapper::toDomain($model);
    }

    private function saveSections(Event $entity, EventModel $model): void
    {
        //* @var EventSectionModel[] $sections */
        $sections = $entity->sections()
            ->map(function ($section) {
                $entityArray = $section->toArray();
                $model = EventSectionModel::find($entityArray['id']) ?? EventSectionMapper::toModel($section);
                $model->fill([
                    'id' => $entityArray['id'] ?? null,
                    'name' => $entityArray['name'] ?? null,
                    'description' => $entityArray['description'] ?? null,
                    'is_published' => $entityArray['is_published'] ?? false,
                    'event_id' => $entityArray['event_id'] ?? null,
                    'total_spots' => $entityArray['total_spots'] ?? 0,
                    'total_spots_reserved' => $entityArray['total_spots_reserved'] ?? 0,
                ]);
                return $model;
            });

        $model->sections()->saveMany($sections);

        foreach ($entity->sections() as $section) {
            $entityArray = $section->toArray();
            $sectionModel = EventSectionModel::find($entityArray['id']) ?? EventSectionMapper::toModel($section);

            $spots = $section->spots()
                ->map(function ($spot) {
                    $entityArray = $spot->toArray();
                    $model = EventSpotModel::find($entityArray['id']) ?? EventSpotMapper::toModel($spot);
                    $model->fill([
                        'id' => $entityArray['id'] ?? null,
                        'location' => $entityArray['location'] ?? null,
                        'is_reserved' => $entityArray['is_reserved'] ?? false,
                        'is_published' => $entityArray['is_published'] ?? false,
                        'event_section_id' => $entityArray['event_section_id'] ?? null,
                    ]);
                    return $model;
                });

            $sectionModel->spots()->saveMany($spots);
        }
    }
}