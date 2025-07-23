<?php

namespace App\Events\Application;

use App\Common\Infra\UnitOfWorkEloquent;
use App\Events\Infra\Repository\OrderRepository;
use Throwable;

class OrderService
{
    public function __construct(
        private OrderRepository $eventRepository,
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
     *    customerId: string,
     *    eventSpotId: string,
     *    amount: float,
     * } $input
     * @return array
     * @throws Throwable
     */
    public function create(array $input): array{
        $order = $this->eventRepository->createOrder(
            $input['customerId'],
            $input['eventSpotId'],
            $input['amount']
        );

        $this->unitOfWork->register($order);
        $this->unitOfWork->commit();

        return $order->toArray();
    }
}