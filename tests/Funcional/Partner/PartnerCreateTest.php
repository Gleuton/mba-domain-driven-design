<?php

namespace Funcional\Partner;

use Database\Factories\PartnerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PartnerCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testCreatePartners(): void
    {
        $response = $this->postJson(
            '/api/partner',
            [
                'name' => 'Test Partner',
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
        ]);
        $response->assertJson([
            'name' => 'Test Partner',
        ]);
    }

    public function testCreatePartnerWithInvalidData(): void{
        $response = $this->postJson('/api/partner', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

}
