<?php

namespace App\Events\Domain\Entities\Customer;

use App\Common\Domain\AggregateRoot;
use App\Common\Domain\ValueObjects\Cpf;
use App\Common\Domain\ValueObjects\Name;
use DomainException;

class Customer extends AggregateRoot
{
    private function __construct(
        private Cpf $cpf,
        private Name $name,
        private readonly CustomerId $id
    ) {
    }

    /**
     * @param array{cpf: Cpf, name: Name, id?: string} $command
     * @throws DomainException
     */
    public static function create(array $command): self
    {
        if (empty($command['cpf']) || empty($command['name'])) {
            throw new DomainException('CPF e Nome são obrigatórios');
        }

        return new self(
            cpf: new Cpf($command['cpf']),
            name: new Name($command['name']),
            id: new CustomerId($command['id'] ?? null)
        );
    }

    public function changeName(string $name): void
    {
        if (empty($name)) {
            throw new DomainException('Nome é obrigatório');
        }

        $this->name = new Name($name);
    }

    public function changeCpf(string $cpf): void
    {
        if (empty($cpf)) {
            throw new DomainException('CPF é obrigatório');
        }

        $this->cpf = new Cpf($cpf);
    }

    protected function serializableFields(): array
    {
        return [
            'cpf' => $this->cpf->getValue(),
            'name' => $this->name->getValue(),
            'id' => $this->id->getValue(),
        ];
    }
}