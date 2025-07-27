<?php

namespace App\Events\Application;

use App\Common\Application\UnitOfWorkInterface;
use App\Common\Domain\ValueObjects\Name;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventCollection;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\PartnerRepository;
use DateTimeImmutable;
use Throwable;

readonly class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private PartnerRepository $partnerRepository,
        private UnitOfWorkInterface $unitOfWork
    ) {
    }

    public function list(): EventCollection
    {
        return $this->eventRepository
            ->findAll();
    }

    /**
     * @param array{
     *    id?: string|null,
     *    name: string,
     *    description?: string|null,
     *    date: string,
     *    partner_id: string,
     * } $input
     * @return Event
     * @throws Throwable
     */
    public function create(array $input): Event
    {
        $partner = $this->partnerRepository->findById(new EventId($input['partner_id']));

        $event = $partner->eventInit(
            new Name($input['name']),
            $input['description'] ?? null,
            new DateTimeImmutable($input['date'])
        );

        $this->unitOfWork->register($event);
        $this->unitOfWork->commit();

        return $event;
    }

    public function findSections(string $eventId): array
    {
        $event = $this->eventRepository->findById(new EventId($eventId));

        return $event->sections()->toArray();
    }

    public function publishAll(string $id): Event
    {
        $event = $this->eventRepository->findById(new EventId($id));
        $event->initializeEventSpots();

        $event->publishAll();
        $this->unitOfWork->register($event);
        $this->unitOfWork->commit();

        return $event;
    }
}