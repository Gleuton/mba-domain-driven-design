<?php

namespace App\Events\Domain\Repositories;

use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerCollection;
use App\Events\Domain\Entities\Customer\CustomerId;

interface CustomerRepositoryInterface
{
    public function save(Customer $entity): void;

    public function findById(CustomerId $id): Customer;

    public function findAll(): CustomerCollection;

    public function remove(Customer $entity): void;
}