<?php

namespace Tests\Events\Domain\Entities;

use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\Partner\Partner;
use PHPUnit\Framework\TestCase;

class PartnerTest extends TestCase
{
    public function testCreatePartnerWithEvent(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro Teste',
            'id' => '123e4567-e89b-12d3-a456-426614174000',
        ]);

        $event = $partner->eventInit(
            name: new Name('Evento de Teste'),
            description: 'Descrição do Evento',
            date: new \DateTimeImmutable('2023-10-01 10:00:00')
        )->toArray();

        $this->assertEquals('Evento de Teste', $event['name']);
        $this->assertEquals('Descrição do Evento', $event['description']);
    }
}
