<?php

namespace Funcional\Event;

use App\Models\PartnerModel;
use Database\Factories\PartnerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EventCreateTest extends TestCase
{
    use RefreshDatabase;
    private PartnerModel $partner;
    public function setUp(): void
    {
        parent::setUp();
        $this->partner = PartnerFactory::new()->create();
    }

    public function testCreateEvent(): void
    {
        $response = $this->postJson(
            '/api/events',
            [
                'name' => 'Test Event',
                'description' => 'This is a test event.',
                'date' => new \DateTimeImmutable('tomorrow')->format('Y-m-d H:i:s'),
                'partner_id' => $this->partner->id,
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'description',
            'date',
            'is_published',
            'partner_id',
        ])->assertJson([
            'name' => 'Test Event',
            'description' => 'This is a test event.',
            'is_published' => false,
            'partner_id' => $this->partner->id,
        ]);
    }

    #[DataProvider('eventProvider')]
    public function testCreateEventWithoutPartner($payload, $expectedStatus, $expectedErrors, $shouldExist): void
    {
        if (isset($payload['partner_id']) && $payload['partner_id'] === 'partner-id') {
            $payload['partner_id'] = $this->partner->id;
        }

        $response = $this->postJson('/api/events', $payload);

        $response->assertStatus($expectedStatus);

        if ($expectedErrors) {
            $response->assertJsonValidationErrors(array_keys($expectedErrors));
            $response->assertJson(['errors' => $expectedErrors]);
        }

        if (!$shouldExist) {
            $this->assertDatabaseMissing('events', [
                'name' => $payload['name'] ?? null,
                'description' => $payload['description'] ?? null,
                'date' => $payload['date'] ?? null,
                'partner_id' => $payload['partner_id'] ?? null,
            ]);
        }

        if ($shouldExist) {
            $this->assertDatabaseHas('events', [
                'name' => $payload['name'] ?? null,
                'description' => $payload['description'] ?? null,
                'date' => $payload['date'] ?? null,
                'partner_id' => $payload['partner_id'] ?? null,
            ]);
        }
    }

    public static function eventProvider(): array
    {
        return [
            'sem parceiro' => [
                [
                    'name' => 'Test Event Without Partner',
                    'description' => 'This event should fail without a partner.',
                    'date' => new \DateTimeImmutable('tomorrow')->format('Y-m-d H:i:s'),
                ],
                422,
                ['partner_id' => ['O ID do parceiro é obrigatório.']],
                false,
            ],
            'sem nome' => [
                [
                    'description' => 'This event should fail without a name.',
                    'date' => new \DateTimeImmutable('tomorrow')->format('Y-m-d H:i:s'),
                    'partner_id' => 'partner-id',
                ],
                422,
                ['name' => ['O nome do evento é obrigatório.']],
                false,
            ],
            'sem data' => [
                [
                    'name' => 'Test Event Without Date',
                    'description' => 'This event should fail without a date.',
                    'partner_id' => 'partner-id',
                ],
                422,
                ['date' => ['A data do evento é obrigatória.']],
                false,
            ],
            'sem descrição' => [
                [
                    'name' => 'Test Event Without Description',
                    'date' => new \DateTimeImmutable('tomorrow')->format('Y-m-d H:i:s'),
                    'partner_id' => 'partner-id',
                ],
                201,
                null,
                true,
            ],
        ];
    }
}
