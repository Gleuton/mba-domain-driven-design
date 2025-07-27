<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\AbstractEntity;
use App\Common\Domain\ValueObjects\Cpf;
use App\Common\Domain\ValueObjects\Uuid;
use App\Common\Infra\RepositoryInterface;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerCollection;
use App\Events\Infra\Mapper\CustomerMapper;
use App\Models\CustomerModel;
use App\Models\PartnerModel;
use Exception;
use RuntimeException;

class CustomerRepository implements RepositoryInterface
{
    public function save(AbstractEntity $entity): void
    {
        if (!$entity instanceof Customer) {
            throw new RuntimeException("Invalid entity type");
        }
        $entityArray = $entity->toArray();
        $model = CustomerModel::find($entityArray['id']) ?? CustomerMapper::toModel($entity);

        $model->name = $entityArray['name'];
        $model->cpf = $entityArray['cpf'];

        $model->save();
    }

    public function findById(Uuid $id): Customer
    {
        $partnerModel = CustomerModel::find($id->getValue());

        if (!$partnerModel) {
            throw new RuntimeException("Customer not found");
        }

        return CustomerMapper::toDomain($partnerModel);
    }

    public function findAll(): CustomerCollection
    {
        $models = PartnerModel::all();
        $collection = new CustomerCollection();

        foreach ($models as $model) {
            $collection->add(CustomerMapper::toDomain($model));
        }

        return $collection;
    }

    public function remove(AbstractEntity $entity): void
    {
        if (!$entity instanceof Customer) {
            throw new RuntimeException("Invalid entity type");
        }

        $entityArray = $entity->toArray();

        $model = CustomerModel::find($entityArray['id']);
        $model->delete();
    }

    public function findByCpf(Cpf $cpf): Customer
    {
        $model = CustomerModel::where('cpf', $cpf->getValue())->first();

        if (!$model) {
            throw new RuntimeException("Customer not found");
        }

        return CustomerMapper::toDomain($model);
    }

}