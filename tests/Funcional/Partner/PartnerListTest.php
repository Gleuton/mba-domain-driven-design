<?php

namespace Tests\Funcional\Partner;

use Database\Factories\PartnerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerListTest extends TestCase
{
    use RefreshDatabase;

    public function testListPartners(): void
    {
        PartnerFactory::new()->count(3)->create();

        $response = $this->get('/api/partners');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
            ]
        ]);
        $response->assertJsonCount(3);
    }
}
