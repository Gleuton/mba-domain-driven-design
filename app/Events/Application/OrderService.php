<?php

namespace App\Events\Application;

use App\Common\Application\UnitOfWorkInterface;
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
use InvalidArgumentException;
use Throwable;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CustomerRepository $customerRepository,
        private EventRepository $eventRepository,
        private SpotReservationRepository $spotReservationRepository,
        private UnitOfWorkInterface $unitOfWork
    ) {
    }

    public function list(): OrderCollection
    {
        return $this->orderRepository
            ->findAll();
    }

    /**
     * @param array{
     *    event_id :string,
     *    customer_id: string,
     *    event_section_id: string,
     *    event_spot_id: string,
     * } $input
     * @return SpotReservation
     * @throws Throwable
     */
    public function create(array $input): SpotReservation
    {
        $sectionId = new EventSectionId($input['event_section_id']);
        $eventSpotId = new EventSpotId($input['event_spot_id']);
        $customerId = new CustomerId($input['customer_id']);
        $eventId = new EventId($input['event_id']);

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
            'reservedAt' => null
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

        return $reservation;
    }
}