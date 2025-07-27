<?php

namespace Funcional\Partner;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerCreateTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatePartners(): void
    {
        $response = $this->postJson(
            '/api/partners',
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

    public function testCreatePartnerWithNameNull(): void{
        $response = $this->postJson('/api/partners', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonFragment([
            'name' => ['O nome do parceiro é obrigatório.']
        ]);
    }

    public function testCreatePartnerWithNameLessThanThreeCharacters(): void{
        $response = $this->postJson('/api/partners', [
            'name' => 'ab',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonFragment([
            'name' => ['O nome do parceiro deve ter pelo menos 3 caracteres.']
        ]);
    }

}
