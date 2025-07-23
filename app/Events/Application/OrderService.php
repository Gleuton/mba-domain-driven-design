<?php

namespace App\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\Event\EventId;
use App\Events\Domain\Entities\EventSection\EventSectionId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Infra\Repository\CustomerRepository;
use App\Events\Infra\Repository\EventRepository;
use App\Events\Infra\Repository\OrderRepository;
use InvalidArgumentException;
use Throwable;

readonly class OrderService
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
        $sectionId = new EventSectionId($input['sectionId']);
        $eventSpotId = new EventSpotId($input['eventSpotId']);
        $customer = $this->customerRepository->findById(new CustomerId($input['customerId']));
        $event = $this->eventRepository->findById(new EventId($input['eventId']));

        if (!$event->allowReserveSpots($sectionId, $eventSpotId)){
            throw new InvalidArgumentException('Spot reservation is not allowed for this event or section');
        }

        $order = Order::create([
            'customerId' => $input['customerId'],
            'eventSpotId' => $input['eventSpotId'],
            'amount' => $input['amount'],
        ]);

        $this->unitOfWork->register($order);
        $this->unitOfWork->commit();

        return $order->toArray();
    }
}