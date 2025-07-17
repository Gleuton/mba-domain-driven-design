<?php

namespace Events\Infra\Repository;

use App\Common\Domain\ValueObjects\Cpf;
use App\Events\Domain\Entities\Customer\Customer;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Infra\Repository\CustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class CustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testSaveCustomer(): void
    {
        $cpf = new Cpf('600.527.660-39');
        $customer = Customer::create([
            'name' => 'Cliente de Teste',
            'cpf' => $cpf,
        ]);
        $repository = new CustomerRepository();
        $repository->save($customer);
        $this->assertDatabaseHas('customers', [
            'cpf' => $cpf->getValue(),
            'name' => 'Cliente de Teste',
        ]);

    }

    public function testFindCustomerByCpf(): void
    {
        $cpf = new Cpf('600.527.660-39');
        $customer = Customer::create([
            'name' => 'Cliente de Teste',
            'cpf' => $cpf,
        ]);

        $repository = new CustomerRepository();
        $repository->save($customer);
        $foundCustomer = $repository->findByCpf($cpf);
        $foundCustomerArray = $foundCustomer->toArray();

        $this->assertEquals($cpf->getValue(), $foundCustomerArray['cpf']);
        $this->assertEquals('Cliente de Teste', $foundCustomerArray['name']);
    }

    public function testFindCustomerById():void
    {
        $cpf = new Cpf('600.527.660-39');
        $customer = Customer::create([
            'name' => 'Cliente de Teste',
            'cpf' => $cpf,
        ]);

        $customerArray = $customer->toArray();

        $repository = new CustomerRepository();
        $repository->save($customer);

        $foundCustomer = $repository->findById(new CustomerId($customerArray['id']));
        $foundCustomerArray = $foundCustomer->toArray();

        $this->assertEquals($cpf->getValue(), $foundCustomerArray['cpf']);
        $this->assertEquals('Cliente de Teste', $foundCustomerArray['name']);
    }


    public function testRemoveCustomer(): void
    {
        $cpf = new Cpf('600.527.660-39');
        $customer = Customer::create([
            'name' => 'Cliente de Teste',
            'cpf' => $cpf,
        ]);

        $repository = new CustomerRepository();
        $repository->save($customer);
        $repository->remove($customer);

        $this->assertDatabaseMissing('customers', [
            'cpf' => $cpf->getValue(),
            'name' => 'Cliente de Teste',
        ]);
    }
}
