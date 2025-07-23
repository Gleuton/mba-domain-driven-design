<?php

namespace App\Events\Domain\Entities\Order;

use App\Common\Domain\AggregateRoot;
use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;

class Order extends AggregateRoot
{
    private function __construct(
        private OrderId $id,
        private CustomerId $customerId,
        private float $amount,
        private EventSpotId $eventSpotId,
        private OrderStatus $status,
    ) {
    }

    /**
     * @param array{
     *     orderId?: string,
     *     customerId: string,
     *     amount: float,
     *     eventSpotId: string
     * } $data
     * @return Order
     */
    public static function create(array $data = []): Order
    {
        return new self(
            new OrderId($data['orderId'] ?? null),
            new CustomerId($data['customerId']),
            $data['amount'] ?? 0.0,
            new EventSpotId($data['eventSpotId']),
            OrderStatus::PENDING
        );
    }

    public function serializableFields(): array
    {
        return [
            'order_id' => $this->id->getValue(),
            'customer_id' => $this->customerId,
            'amount' => $this->amount,
            'event_spot_id' => $this->eventSpotId->getValue(),
            'status' => $this->status->value,
        ];
    }
}