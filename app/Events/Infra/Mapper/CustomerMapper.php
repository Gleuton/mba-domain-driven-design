<?php

namespace App\Events\Infra\Mapper;

use App\Events\Domain\Entities\Customer\Customer;
use App\Models\CustomerModel;

class CustomerMapper
{
    public static function toModel(Customer $entity): CustomerModel
    {
        $entityArray= $entity->toArray();

        return new CustomerModel([
            'id' => $entityArray['id'],
            'name' => $entityArray['name'],
            'cpf' => $entityArray['cpf'],
        ]);
    }

    public static function toDomain(CustomerModel $model): Customer
    {
        return Customer::create([
            'id' => $model->id,
            'name' => $model->name,
            'cpf' => $model->cpf,
        ]);
    }
}