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
        $model = SpotReservationModel::find($entityArray['id']) ?? SpotReservationMapper::toModel($entity);

        $model->save();
    }

    public function findById(Uuid $id): SpotReservation
    {
        $model = SpotReservationModel::find($id->getValue());

        if (!$model) {
            throw new Exception("Model not found");
        }

        return SpotReservationMapper::toDomain($model);
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

        $model = SpotReservationModel::find($entityArray['id']);
        $model->delete();
    }
}