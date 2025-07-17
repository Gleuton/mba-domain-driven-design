<?php

namespace Tests\Events\Domain\Entities;

use App\Events\Domain\Entities\Customer\Customer;
use DomainException;
use Tests\TestCase;


class CustomerTest extends TestCase
{
    public function testCreateCustomerWithValidData(): void
    {
        $customer = Customer::create([
            'cpf' => '763.628.700-50',
            'name' => 'John Doe',
            'id' => '123e4567-e89b-12d3-a456-426614174000'
        ])->toArray();

        $this->assertEquals('76362870050', $customer['cpf']);
        $this->assertEquals('John Doe', $customer['name']);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $customer['id']);
    }

    public function testCreateCustomerJson(): void
    {
        $payload = [
            'cpf' => '76362870050',
            'name' => 'John Doe',
            'id' => '123e4567-e89b-12d3-a456-426614174000'
        ];
        $customer = Customer::create($payload);

        $this->assertJsonStringEqualsJsonString(json_encode($payload), $customer);
    }

    public function testCreateCustomerWithoutUuid(): void
    {
        $customer = Customer::create([
            'cpf' => '763.628.700-50',
            'name' => 'John Doe',
        ])->toArray();

        $this->assertEquals('76362870050', $customer['cpf']);
        $this->assertEquals('John Doe', $customer['name']);
        $this->assertNotNull($customer['id']);
    }

    public function testCreateCustomerWithMissingCpfThrowsException(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('CPF e Nome são obrigatórios');
        Customer::create(['name' => 'John Doe']);
    }


    public function testCreateCustomerWithMissingNameThrowsException(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('CPF e Nome são obrigatórios');
        Customer::create(['cpf' => '12345678901']);
    }
}
