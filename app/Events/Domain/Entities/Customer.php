<?php

namespace App\Events\Domain\Entities;

use App\Common\Domain\AggregateRoot;
use App\Common\Domain\ValueObjects\Cpf;
use App\Common\Domain\ValueObjects\Name;
use DomainException;

class Customer extends AggregateRoot
{
    private function __construct(
        private readonly Cpf $cpf,
        private readonly Name $name,
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

    protected function serializableFields(): array
    {
        return [
            'cpf' => $this->cpf->getValue(),
            'name' => $this->name->getValue(),
            'id' => $this->id->getValue(),
        ];
    }
}