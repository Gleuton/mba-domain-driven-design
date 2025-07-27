<?php

namespace Events\Infra\Repository;

use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\Partner\Partner;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\PartnerRepository;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EventRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testSaveEvent(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $eventSection = EventSection::create([
            'name' => 'Seção de Teste',
            'description' => 'Descrição da Seção',
            'price' => 100.0,
            'totalSpots' => 50,
            'totalSpotsReserved' => 0,
            'isPublished' => false,
        ]);

        $event->addSection($eventSection);
        $event->initializeEventSpots();

        $repository = new EventRepository();
        $repository->save($event);
        $this->assertDatabaseHas('events', [
            'id' => $event->toArray()['id'],
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
        ]);

        $this->assertDatabaseHas('partners', [
            'id' => $partner->toArray()['id'],
            'name' => 'Parceiro de Teste',
        ]);

        $this->assertDatabaseHas('event_sections', [
            'id' => $eventSection->toArray()['id'],
            'name' => 'Seção de Teste',
            'description' => 'Descrição da Seção',
            'price' => 100,
            'total_spots' => 50,
            'total_spots_reserved' => 0,
            'is_published' => false,
        ]);

        $this->assertDatabaseCount('event_spots', 50);
        $this->assertDatabaseHas('event_spots', [
            'event_section_id' => $eventSection->toArray()['id'],
            'is_reserved' => false,
        ]);
    }

    public function testFindEvent(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $repository = new EventRepository();
        $repository->save($event);

        $eventId = $event->toArray()['id'];
        $foundEvent = $repository->findById(new EventId($eventId));
        $this->assertEquals($event->toArray(), $foundEvent->toArray());
    }

    public function testChangeName(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $repository = new EventRepository();
        $repository->save($event);

        $event->changeName(new Name('Evento Atualizado'));
        $repository->save($event);

        $this->assertDatabaseHas('events', [
            'id' => $event->toArray()['id'],
            'name' => 'Evento Atualizado',
        ]);
    }

    public function testChangeDescription(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $repository = new EventRepository();
        $repository->save($event);

        $event->changeDescription('Descrição Atualizado');
        $repository->save($event);

        $this->assertDatabaseHas('events', [
            'id' => $event->toArray()['id'],
            'description' => 'Descrição Atualizado',
        ]);
    }

    public function testChangeDate(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $repository = new EventRepository();
        $repository->save($event);

        $event->changeDate(new DateTimeImmutable('2023-11-01 10:00:00'));
        $repository->save($event);

        $this->assertDatabaseHas('events', [
            'id' => $event->toArray()['id'],
            'date' => '2023-11-01 10:00:00',
        ]);
    }

    public function testRemoveEvent(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);
        $eventId = $event->toArray()['id'];

        $repository = new EventRepository();
        $repository->save($event);

        $repository->remove($event);

        $this->assertDatabaseMissing('events', [
            'id' => $eventId,
            'name' => 'Evento de Teste',
        ]);
    }


    public function testPublishAllSections(): void
    {
        $partner = Partner::create([
            'name' => 'Parceiro de Teste',
        ]);
        $partnerRepository = new PartnerRepository();
        $partnerRepository->save($partner);

        $event = Event::create([
            'name' => 'Evento de Teste',
            'description' => 'Descrição do Evento',
            'date' => '2023-10-01 10:00:00',
            'partnerId' => $partner->toArray()['id'],
        ]);

        $repository = new EventRepository();
        $repository->save($event);

        $event->publishAll();
        $repository->save($event);

        $this->assertDatabaseHas('events', [
            'id' => $event->toArray()['id'],
            'is_published' => true,
        ]);
    }
}
