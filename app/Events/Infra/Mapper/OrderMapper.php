<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\Order\Order;
use App\Models\OrderModel;

class OrderMapper
{
    public static function toModel(Order $order): OrderModel
    {
        $entityArray = $order->toArray();

        return new OrderModel([
            'customer_id' => $entityArray['customer_id'],
            'event_spot_id' => $entityArray['event_spot_id'],
            'amount' => $entityArray['amount'],
            'status' => $entityArray['status'],
        ]);
    }

    public static function toDomain(OrderModel $model): Order
    {
        return Order::create([
            'customerId' => $model->customer_id,
            'eventSpotId' => $model->event_spot_id,
            'amount' => $model->amount,
        ]);
    }
}