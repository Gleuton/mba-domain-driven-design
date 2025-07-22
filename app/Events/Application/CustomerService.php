<?php

namespace App\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Infra\Repository\CustomerRepository;
use Exception;
use RuntimeException;

readonly class CustomerService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private UnitOfWorkEloquent $unitOfWork
    ) {
    }

    public function list(): array
    {
        return $this->customerRepository
            ->findAll()
            ->toArray();
    }

    public function register(array $input): array
    {
        $customer = Customer::create($input);

        $this->unitOfWork->register($customer);
        $this->unitOfWork->commit();

        return $customer->toArray();
    }

    public function update(array $input): array
    {
        $customer = $this->customerRepository->findById($input['id']);

        $customer->changeName($input['name']);
        $customer->changeCpf($input['cpf']);

        $this->unitOfWork->register($customer);
        $this->unitOfWork->commit();

        return $customer->toArray();
    }
}