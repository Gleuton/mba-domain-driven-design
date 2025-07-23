<?php

namespace App\Events\Application;

use App\Common\Domain\AbstractEntity;
use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\Event\Event;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Entities\EventSection\EventSection;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpot;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Infra\Repository\CustomerRepository;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\OrderRepository;
use InvalidArgumentException;
use Throwable;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CustomerRepository $customerRepository,
        private EventRepository $eventRepository,
        private UnitOfWorkEloquent $unitOfWork
    ) {
    }

    public function list(): array
    {
        return $this->orderRepository
            ->findAll()
            ->toArray();
    }

    /**
     * @param array{
     *    eventId :string,
     *    customerId: string,
     *    sectionId: string,
     *    eventSpotId: string,
     *    amount: float,
     * } $input
     * @return array
     * @throws Throwable
     */
    public function create(array $input): array
    {
        $customer = $this->customerRepository->findById(new CustomerId($input['customerId']));
        $event = $this->eventRepository->findById(new EventId($input['eventId']));
        $section = $this->getSection($event, $input['sectionId']);
        $spot = $this->getSpot($section, $input['eventSpotId']);

        $order = Order::create([
            'customerId' => $input['customerId'],
            'eventSpotId' => $input['eventSpotId'],
            'amount' => $input['amount'],
        ]);

        $this->unitOfWork->register($order);
        $this->unitOfWork->commit();

        return $order->toArray();
    }


    public function getSpot(EventSection $section, EventSpotId $eventSpotId): EventSpot
    {
        /**
         * @var EventSpot $spot
         */
        $spot = $section->spots()->find(
            static fn(EventSpot $entity) => $entity->equals(new EventSpotId($eventSpotId))
        );
        if (!$spot) {
            throw new InvalidArgumentException('Event spot not found');
        }

        return $spot;
    }

    private function getSection(Event $event, EventSectionId $sectionId): EventSection
    {
        /**
         * @var EventSection $section
         */
        $section = $event->sections()->find(
            static fn(EventSection $entity) => $entity->equals(new EventSectionId($sectionId))
        );
        
        if (!$section) {
            throw new InvalidArgumentException('Section not found');
        }
        return $section;
    }
}