<?php

namespace Tests\Events\Infra\Repository;

use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Domain\Entities\Partner\PartnerId;
use App\Events\Infra\Repository\PartnerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PartnerRepositoryTest extends TestCase
{
    use RefreshDatabase;
    public function testSavePartner(): void
    {
        $partner = Partner::create([
            'name' => 'Meu Parceiro 1'
        ]);
        $repository = new PartnerRepository();
        $repository->save($partner);

        $this->assertDatabaseHas('partners', [
            'id' => $partner->toArray()['id'],
            'name' => 'Meu Parceiro 1'
        ]);
    }

    public function testFindPartner(): void
    {
        $partner = Partner::create([
            'name' => 'Meu Parceiro 2'
        ]);
        $repository = new PartnerRepository();
        $repository->save($partner);
        $partnerId = new PartnerId($partner->toArray()['id']);

        $foundPartner = $repository->findById($partnerId);

        $this->assertEquals($partner->toArray(), $foundPartner->toArray());
    }

    public function testChangeName(): void
    {
        $partner = Partner::create([
            'name' => 'Meu Parceiro 3'
        ]);
        $repository = new PartnerRepository();
        $repository->save($partner);

        $partner->changeName(new Name('Parceiro Atualizado'));
        $repository->save($partner);

        $this->assertDatabaseHas('partners', [
            'id' => $partner->toArray()['id'],
            'name' => 'Parceiro Atualizado'
        ]);
    }
}
