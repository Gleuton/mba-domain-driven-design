<?php

namespace App\Events\Application;

use App\Common\Domain\ValueObjects\Name;
use App\Common\Infra\UnitOfWorkEloquent;
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
        private UnitOfWorkEloquent $unitOfWork
    ) {
    }

    public function list(): array
    {
        return $this->eventRepository
            ->findAll()
            ->toArray();
    }

    /**
     * @param array{
     *    id?: string|null,
     *    name: string,
     *    description?: string|null,
     *    date: string,
     *    partnerId: string,
     * } $input
     * @return array
     * @throws Throwable
     */
    public function create(array $input): array
    {
        $partner = $this->partnerRepository->findById($input['partnerId']);

        $event = $partner->eventInit(
            new Name($input['name']),
            $input['description'] ?? null,
            new DateTimeImmutable($input['date'])
        );

        $this->unitOfWork->register($event);
        $this->unitOfWork->commit();

        return $event->toArray();
    }

    public function findSections(string $eventId): array
    {
        $event = $this->eventRepository->findById(new EventId($eventId));

        return $event->sections()->toArray();
    }
}