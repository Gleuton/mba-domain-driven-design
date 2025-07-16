<?php

namespace Events\Domain\Entities;

use App\Events\Domain\Entities\Event;
use App\Events\Domain\Entities\EventSection;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testCreateEventWithSection(): void
    {
        $event = Event::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'location' => 'Local do Evento',
        ]);

        $section = EventSection::create([
            'name' => 'Seção 1',
            'description' => 'Descrição da Seção 1',
            'total_spots' => 100,
            'price' => 100.0,
        ]);

        $section2 = EventSection::create([
            'name' => 'Seção 2',
            'description' => 'Descrição da Seção 1',
            'total_spots' => 10,
            'price' => 10.1,
        ]);

        $event->addSection($section);
        $event->addSection($section2);

        $eventSections = $event->toArray()['event_sections'];
        $this->assertCount(2, $eventSections);
        $this->assertEquals('Seção 1', $eventSections[0]['name']);
        $this->assertEquals('Seção 2', $eventSections[1]['name']);
        $this->assertEquals(110, $event->totalSpots());
    }


    public function testPublishAllSections(): void
    {
        $event = Event::create([
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partner_id' => '123e4567-e89b-12d3-a456-426614174001',
        ]);

        $section1 = EventSection::create([
            'name' => 'Seção 1',
            'description' => 'Descrição da Seção 1',
            'total_spots' => 100,
            'price' => 100.0,
        ]);

        $section2 = EventSection::create([
            'name' => 'Seção 2',
            'description' => 'Descrição da Seção 2',
            'total_spots' => 50,
            'price' => 50.0,
        ]);

        $event->addSection($section1);
        $event->addSection($section2);

        $event->publishAll();

        $eventArray = $event->toArray();

        $this->assertTrue($eventArray['is_published']);
        $this->assertTrue($eventArray['event_sections'][0]['is_published']);
        $this->assertTrue($eventArray['event_sections'][1]['is_published']);
    }
}
