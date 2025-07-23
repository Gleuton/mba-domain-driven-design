<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\SpotReservation;
use App\Models\SpotReservationModel;

class SpotReservationMapper
{
    public static function toModel(SpotReservation $entity): SpotReservationModel
    {
        $entityArray = $entity->toArray();
        return new SpotReservationModel([
            'customer_id' => $entityArray['customer_id'],
            'spot_id' => $entityArray['spot_id'],
            'reservation_date' => $entityArray['reservation_date'],
        ]);
    }

    public static function toDomain(SpotReservationModel $model): SpotReservation
    {
        return SpotReservation::create([
            'customerId' => $model->customer_id,
            'eventSpotId' => $model->spot_id,
            'reservation_date' => $model->reservation_date,
        ]);
    }
}