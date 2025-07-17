<?php

namespace App\Events\Infra\Repository;

use App\Common\Domain\ValueObjects\Cpf;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerCollection;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Repositories\CustomerRepositoryInterface;
use App\Events\Infra\Mapper\CustomerMapper;
use App\Models\CustomerModel;
use App\Models\PartnerModel;
use Exception;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function save(Customer $entity): void
    {
        $entityArray = $entity->toArray();
        $model = CustomerModel::find($entityArray['id']) ?? CustomerMapper::toModel($entity);

        $model->name = $entityArray['name'];

        $model->save();
    }

    public function findById(CustomerId $id): Customer
    {
        $partnerModel = CustomerModel::find($id->getValue());

        if (!$partnerModel) {
            throw new Exception("Customer not found");
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

    public function remove(Customer $entity): void
    {
        $entityArray = $entity->toArray();

        $model = CustomerModel::find($entityArray['id']);
        $model->delete();
    }

    public function findByCpf(Cpf $cpf): Customer
    {
        $model = CustomerModel::where('cpf', $cpf->getValue())->first();

        if (!$model) {
            throw new Exception("Customer not found");
        }

        return CustomerMapper::toDomain($model);
    }

}