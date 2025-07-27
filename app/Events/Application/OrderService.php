<?php

namespace App\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Domain\Entities\Order\OrderCollection;
use App\Events\Domain\Entities\SpotReservation;
use App\Events\Infra\Repository\CustomerRepository;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\OrderRepository;
use App\Events\Infra\Repository\SpotReservationRepository;
use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CustomerRepository $customerRepository,
        private EventRepository $eventRepository,
        private SpotReservationRepository $spotReservationRepository,
        private UnitOfWorkEloquent $unitOfWork
    ) {
    }

    public function list(): OrderCollection
    {
        return $this->orderRepository
            ->findAll();
    }

    /**
     * @param array{
     *    eventId :string,
     *    customerId: string,
     *    sectionId: string,
     *    eventSpotId: string,
     * } $input
     * @return array
     * @throws Throwable
     */
    public function create(array $input): array
    {
        $sectionId = new EventSectionId($input['sectionId']);
        $eventSpotId = new EventSpotId($input['eventSpotId']);
        $customerId = new CustomerId($input['customerId']);
        $eventId = new EventId($input['eventId']);

        $this->customerRepository->findById($customerId);
        $event = $this->eventRepository->findById($eventId);

        if (!$event->allowReserveSpots($sectionId, $eventSpotId)) {
            throw new InvalidArgumentException('Spot reservation is not allowed for this event or section');
        }
        $spotReservation = $this->spotReservationRepository->findById($eventSpotId);

        if ($spotReservation) {
            throw new InvalidArgumentException('Spot is already reserved');
        }

        $reservation = SpotReservation::create([
            'customerId' => $customerId->getValue(),
            'eventSpotId' => $eventSpotId->getValue(),
            'reservedAt' => new DateTimeImmutable(),
        ]);

        $section = $event->sectionById($sectionId);

        $order = Order::create([
            'customerId' => $customerId->getValue(),
            'eventSpotId' => $eventSpotId->getValue(),
            'amount' => $section->price(),
        ]);

        $event->markSpotAsReserved($sectionId, $eventSpotId);

        $this->unitOfWork->register($reservation);
        $this->unitOfWork->register($order);
        $this->unitOfWork->register($event);
        $this->unitOfWork->commit();

        return $reservation->toArray();
    }
}