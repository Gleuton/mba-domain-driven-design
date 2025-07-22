<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Uuid;
use App\Common\Infra\RepositoryInterface;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use App\Events\Infra\Mapper\PartnerMapper;
use App\Models\PartnerModel;
use Exception;

class PartnerRepository implements RepositoryInterface
{
    public function save(AbstractEntity $entity): void
    {
        if (!$entity instanceof Partner) {
            throw new \RuntimeException("Invalid entity type");
        }

        $entityArray = $entity->toArray();
        $model = PartnerModel::find($entityArray['id']) ?? PartnerMapper::toModel($entity);

        $model->name = $entityArray['name'];

        $model->save();
    }

    public function findById(Uuid $id): Partner
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

    public function remove(AbstractEntity $entity): void
    {
        if (!$entity instanceof Partner) {
            throw new \RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();

        $model = PartnerModel::find($entityArray['id']);
        $model->delete();
    }

}