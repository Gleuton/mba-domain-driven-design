<?php

namespace Tests\Funcional\Event;

use Database\Factories\EventSectionsFactory;
use Database\Factories\EventsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventListTest extends TestCase
{
    use RefreshDatabase;

    public function testListEvents(): void
    {
        EventsFactory::new()->count(3)->create();

        $response = $this->get('/api/events');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description',
                'date',
                'is_published',
                'total_spots',
                'total_spots_reserved',
                'partner_id',
                'event_sections'
            ]
        ]);
        $response->assertJsonCount(3);
    }

    public function testListEventsEmpty(): void
    {
        $response = $this->get('/api/events');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonCount(0);
    }
}
