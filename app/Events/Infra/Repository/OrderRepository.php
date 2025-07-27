<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Uuid;
use App\Common\Infra\RepositoryInterface;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Domain\Entities\Order\OrderCollection;
use App\Events\Domain\Entities\Order\OrderStatus;
use App\Events\Infra\Mapper\OrderMapper;
use App\Models\OrderModel;
use Exception;
use RuntimeException;

class OrderRepository implements RepositoryInterface
{
    public function save(AbstractEntity $entity): void
    {
        if (!$entity instanceof Order) {
            throw new RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();
        $model = OrderModel::find($entityArray['id']) ?? OrderMapper::toModel($entity);
        $model->fill([
            'id' => $entityArray['id'] ?? null,
            'amount' => $entityArray['amount'] ?? 0,
            'status' => $entityArray['status'] ?? OrderStatus::PENDING,
            'customer_id' => $entityArray['customer_id'] ?? null,
            'event_spot_id' => $entityArray['event_spot_id'] ?? null,
        ]);

        $model->save();
    }

    public function findById(Uuid $id): Order
    {
        $model = OrderModel::find($id->getValue());

        if (!$model) {
            throw new Exception("Model not found");
        }

        return OrderMapper::toDomain($model);
    }

    public function findAll(): OrderCollection
    {
        $models = OrderModel::all();
        $collection = new OrderCollection();

        foreach ($models as $model) {
            $collection->add(OrderMapper::toDomain($model));
        }

        return $collection;
    }

    public function remove(AbstractEntity $entity): void
    {
        if (!$entity instanceof Order) {
            throw new \RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();

        $model = OrderModel::find($entityArray['id']);
        $model->delete();
    }


}