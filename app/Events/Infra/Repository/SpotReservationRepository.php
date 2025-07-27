<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Uuid;
use App\Common\Infra\RepositoryInterface;
use App\Events\Domain\Entities\SpotReservation;
use App\Events\Domain\Entities\SpotReservationCollection;
use App\Events\Infra\Mapper\SpotReservationMapper;
use App\Models\SpotReservationModel;
use Exception;
use RuntimeException;

class SpotReservationRepository implements RepositoryInterface
{
    public function save(AbstractEntity $entity): void
    {
        if (!$entity instanceof SpotReservation) {
            throw new RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();
        $model = SpotReservationModel::find($entityArray['event_spot_id']) ?? SpotReservationMapper::toModel($entity);

        $model->fill([
            'spot_id' => $entityArray['event_spot_id'] ?? null,
            'customer_id' => $entityArray['customer_id'] ?? null,
            'reservation_date' => $entityArray['reservation_date'] ?? null,
        ]);

        $model->save();
    }

    public function findById(Uuid $id): ?SpotReservation
    {
        $models = SpotReservationModel::where('spot_id', $id->getValue())->get();
        if ($models->isEmpty()) {
            return null;
        }

        return SpotReservationMapper::toDomain($models->first());
    }

    public function findAll(): SpotReservationCollection
    {
        $models = SpotReservationModel::all();
        $collection = new SpotReservationCollection();

        foreach ($models as $model) {
            $collection->add(SpotReservationMapper::toDomain($model));
        }

        return $collection;
    }

    public function remove(AbstractEntity $entity): void
    {
        if (!$entity instanceof SpotReservation) {
            throw new \RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();

        $model = SpotReservationModel::find($entityArray['event_spot_id']);
        $model->delete();
    }
}