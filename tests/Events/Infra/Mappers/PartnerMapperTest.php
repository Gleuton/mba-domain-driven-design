<?php

namespace Tests\Events\Infra\Mappers;

use App\Events\Domain\Entities\Partner;
use App\Events\Infra\Mappers\PartnerMapper;
use App\Models\PartnerModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerMapperTest extends TestCase
{
    use RefreshDatabase;

    public function testSaveAndRetrievePartner(): void
    {
        $partner = Partner::create([
            'name' => 'Meu Parceiro'
        ]);
        $partnerId = $partner->toArray()['id'];

        $partnerModel = PartnerMapper::toModel($partner);

        $partnerModel->save();

        $retrievedModel = PartnerModel::find($partnerId);
        $this->assertNotNull($retrievedModel);

        $retrievedPartner = PartnerMapper::toDomain($retrievedModel);

        $this->assertEquals($partner->toArray(), $retrievedPartner->toArray());
        $this->assertEquals('Meu Parceiro', $retrievedPartner->toArray()['name']);
        $this->assertEquals($partnerId, $retrievedPartner->toArray()['id']);
    }
}