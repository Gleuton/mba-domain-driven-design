<?php

namespace Funcional\Event;

use Database\Factories\EventSectionsFactory;
use Database\Factories\EventsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPublishTest extends TestCase
{
    use RefreshDatabase;

    public function testEventsPublishAll(): void
    {
        $totalSpots = 10;
        $events = EventsFactory::new()->count(3)->create([
            'total_spots' => $totalSpots,
            'is_published' => false,
        ]);

        $section = EventSectionsFactory::new()->create([
            'total_spots' => $totalSpots,
            'event_id' => $events[0]->id,
            'is_published' => false,
        ]);

        $response = $this->putJson('/api/events/' . $events[0]->id . '/publish-all');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'All events published successfully.']);

        $this->assertDatabaseHas('events', [
            'id' => $events[0]->id,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('event_sections', [
            'id' => $section->id,
            'is_published' => true,
        ]);

        $this->assertDatabaseCount('event_spots', $totalSpots);
        $this->assertDatabaseHas('event_spots', [
            'is_published' => true,
        ]);
        $this->assertDatabaseMissing('event_spots', [
            'is_published' => false,
        ]);
    }
}
