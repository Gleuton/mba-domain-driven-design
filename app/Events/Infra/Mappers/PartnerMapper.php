<?php

namespace App\Events\Infra\Mappers;

use App\Events\Domain\Entities\Partner\Partner;
use App\Models\PartnerModel;

class PartnerMapper
{
    public static function toModel(Partner $partner): PartnerModel
    {
        $partnerArray = $partner->toArray();

        return new PartnerModel([
            'id' => $partnerArray['id'] ?? null,
            'name' => $partnerArray['name'],
        ]);
    }

    public static function toDomain(PartnerModel $model): Partner
    {
        return Partner::create([
            'name' => $model->name,
            'id' => $model->id
        ]);
    }
}