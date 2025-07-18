<?php

namespace App\Events\Application;

use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Repositories\CustomerRepositoryInterface;

readonly class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $customerRepository)
    {
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
        $this->customerRepository->save($customer);
        return $customer->toArray();
    }
}