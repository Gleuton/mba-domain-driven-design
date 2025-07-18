<?php

namespace Tests\Events\Application;

use App\Events\Application\CustomerService;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerCollection;
use App\Events\Domain\Repositories\CustomerRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CustomerServiceTest extends TestCase
{
    public function testListCustomers():void
    {
        $customer1 = $this->createMock(Customer::class);
        $customer2 = $this->createMock(Customer::class);

        $customerCollection = $this->createMock(CustomerCollection::class);
        $customerCollection->expects($this->once())
            ->method('toArray')
            ->willReturn([$customer1, $customer2]);

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($customerCollection);

        $customerService = new CustomerService($customerRepository);
        $customers = $customerService->list();
        $this->assertCount(2, $customers);
    }

    public function testRegisterCustomer(): void {
        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Customer::class));

        $customerService = new CustomerService($customerRepository);
        $customerService->register(['name' => 'John Doe', 'cpf' => '843.746.580-09']);

    }
}
