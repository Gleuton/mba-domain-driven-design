<?php

namespace Funcional\Order;

use App\Models\CustomerModel;
use App\Models\EventModel;
use App\Models\EventSectionModel;
use App\Models\EventSpotModel;
use Database\Factories\CustomersFactory;
use Database\Factories\EventSectionsFactory;
use Database\Factories\EventsFactory;
use Database\Factories\EventSpotsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderCreateTest extends TestCase
{
    use RefreshDatabase;
    
    private CustomerModel $customer;
    private EventModel $event;
    private EventSectionModel $section;
    private EventSpotModel $spot;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->customer = CustomersFactory::new()->create();
        $this->event = EventsFactory::new()->create([
            'is_published' => true,
            'total_spots' => 1,
            'total_spots_reserved' => 0
        ]);

        $this->section = EventSectionsFactory::new()->create([
            'event_id' => $this->event->id,
            'is_published' => true,
            'total_spots' => 1,
            'total_spots_reserved' => 0
        ]);

        $this->spot = EventSpotsFactory::new()->create([
            'event_section_id' => $this->section->id,
            'is_published' => true,
            'is_reserved' => false
        ]);
    }
    
    public function testCreateOrder(): void
    {
        $response = $this->postJson(
            '/api/orders',
            [
                'event_id' => $this->event->id,
                'customer_id' => $this->customer->id,
                'event_section_id' => $this->section->id,
                'event_spot_id' => $this->spot->id,
            ]
        );
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'customer_id',
            'event_spot_id',
            'reservation_date'
        ]);

        $this->assertDatabaseHas('spot_reservations', [
            'spot_id' => $this->spot->id,
            'customer_id' => $this->customer->id,
        ]);
    }
    
    #[DataProvider('orderProvider')]
    public function testCreateOrderWithInvalidData($payload, $expectedStatus, $expectedErrors, $shouldExist): void
    {
        // Replace placeholder IDs with actual IDs
        if (isset($payload['event_id']) && $payload['event_id'] === 'event-id') {
            $payload['event_id'] = $this->event->id;
        }
        if (isset($payload['customer_id']) && $payload['customer_id'] === 'customer-id') {
            $payload['customer_id'] = $this->customer->id;
        }
        if (isset($payload['event_section_id']) && $payload['event_section_id'] === 'section-id') {
            $payload['event_section_id'] = $this->section->id;
        }
        if (isset($payload['event_spot_id']) && $payload['event_spot_id'] === 'spot-id') {
            $payload['event_spot_id'] = $this->spot->id;
        }

        $response = $this->postJson('/api/orders', $payload);
        
        $response->assertStatus($expectedStatus);
        
        if ($expectedErrors) {
            $response->assertJsonValidationErrors(array_keys($expectedErrors));
            $response->assertJson(['errors' => $expectedErrors]);
        }
        
        if (!$shouldExist) {
            $this->assertDatabaseMissing('spot_reservations', [
                'spot_id' => $payload['event_spot_id'] ?? null,
                'customer_id' => $payload['customer_id'] ?? null,
            ]);
        }
        
        if ($shouldExist) {
            $this->assertDatabaseHas('spot_reservations', [
                'spot_id' => $payload['event_spot_id'] ?? null,
                'customer_id' => $payload['customer_id'] ?? null,
            ]);
        }
    }
    
    public static function orderProvider(): array
    {
        return [
            'sem evento' => [
                [
                    'customer_id' => 'customer-id',
                    'event_section_id' => 'section-id',
                    'event_spot_id' => 'spot-id',
                ],
                422,
                ['event_id' => ['o campo event_id é obrigatório.']],
                false,
            ],
            'sem cliente' => [
                [
                    'event_id' => 'event-id',
                    'event_section_id' => 'section-id',
                    'event_spot_id' => 'spot-id',
                ],
                422,
                ['customer_id' => ['o campo customer_id é obrigatório.']],
                false,
            ],
            'sem seção' => [
                [
                    'event_id' => 'event-id',
                    'customer_id' => 'customer-id',
                    'event_spot_id' => 'spot-id',
                ],
                422,
                ['event_section_id' => ['o campo event_section_id é obrigatório.']],
                false,
            ],
            'sem spot' => [
                [
                    'event_id' => 'event-id',
                    'customer_id' => 'customer-id',
                    'event_section_id' => 'section-id',
                ],
                422,
                ['event_spot_id' => ['o campo event_spot_id é obrigatório.']],
                false,
            ],
        ];
    }
}