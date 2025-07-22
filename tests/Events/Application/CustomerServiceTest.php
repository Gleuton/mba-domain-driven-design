<?php

namespace Tests\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Application\CustomerService;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerCollection;
use App\Events\Infra\Repository\CustomerRepository;
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

        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($customerCollection);

        $unitOfWork = $this->createMock(UnitOfWorkEloquent::class);


        $customerService = new CustomerService($customerRepository, $unitOfWork, );

        $customers = $customerService->list();
        $this->assertCount(2, $customers);
    }

    public function testRegisterCustomer(): void {
        $customerRepository = $this->createMock(CustomerRepository::class);

        $unitOfWork = $this->createMock(UnitOfWorkEloquent::class);
        $unitOfWork->expects($this->once())
            ->method('register')
            ->with($this->isInstanceOf(Customer::class));

        $customerService = new CustomerService($customerRepository, $unitOfWork);


        $customerService->register(['name' => 'John Doe', 'cpf' => '843.746.580-09']);
    }
}
