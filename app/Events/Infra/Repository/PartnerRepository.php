<?php

namespace App\Events\Infra\Repository;

use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use App\Events\Domain\Entities\Partner\PartnerId;
use App\Events\Domain\Repositories\PartnerRepositoryInterface;
use App\Events\Infra\Mapper\PartnerMapper;
use App\Models\PartnerModel;
use Exception;

class PartnerRepository implements PartnerRepositoryInterface
{
    public function save(Partner $entity): void
    {
        $entityArray = $entity->toArray();
        $model = PartnerModel::find($entityArray['id']) ?? PartnerMapper::toModel($entity);

        $model->name = $entityArray['name'];

        $model->save();
    }

    public function findById(PartnerId $id): Partner
    {
        $partnerModel = PartnerModel::find($id->getValue());

        if (!$partnerModel) {
            throw new Exception("Partner not found");
        }

        return PartnerMapper::toDomain($partnerModel);
    }

    public function findAll(): PartnerCollection
    {
        $partnerModels = PartnerModel::all();
        $collection = new PartnerCollection();

        foreach ($partnerModels as $partnerModel) {
            $collection->add(PartnerMapper::toDomain($partnerModel));
        }

        return $collection;
    }

    public function remove(Partner $entity): void
    {
        $entityArray = $entity->toArray();

        $model = PartnerModel::find($entityArray['id']);
        $model->delete();
    }

}