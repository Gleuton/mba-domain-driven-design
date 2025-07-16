<?php

namespace App\Events\Domain\Entities;

use App\Common\Domain\AggregateRoot;
use App\Common\Domain\ValueObjects\Name;
use DateTimeImmutable;
use DomainException;

class Partner extends AggregateRoot
{
    private function __construct(
        private Name $name,
        private readonly PartnerId $id
    ) {
    }

    /**
     * @param array{name: Name, id?: string} $command
     * @throws DomainException
     */
    public static function create(array $command): self
    {
        if (empty($command['name'])) {
            throw new DomainException('CPF e Nome são obrigatórios');
        }

        return new self(
            name: new Name($command['name']),
            id: new PartnerId($command['id'] ?? null)
        );
    }

    public function eventInit(Name $name, ?string $description, DateTimeImmutable $date): Event
    {
        return Event::create([
            'name' => $name->getValue(),
            'description' => $description,
            'date' => $date->format('Y-m-d H:i:s'),
            'partner_id' => $this->id->getValue(),
        ]);
    }

    public function changeName(Name $name): void
    {
        $this->name = $name;
    }
}