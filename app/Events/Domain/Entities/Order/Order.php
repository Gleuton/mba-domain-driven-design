<?php

namespace App\Events\Domain\Entities\Order;

use App\Common\Domain\AggregateRoot;
use App\Events\Domain\Entities\EventSection\EventSectionId;

class Order extends AggregateRoot
{
    private function __construct(
        private ?OrderId $id,
        private string $customerId,
        private float $amount,
        private EventSectionId $eventSectionId,
    )
    {
    }

    public static function create(?array $data = []): Order {
        return new self(
            new OrderId($data['orderId'] ?? null),
            $data['customerId'] ?? '',
            $data['amount'] ?? 0.0,
            new EventSectionId($data['eventSectionId'] ?? null)
        );
    }

    public function serializableFields(): array
    {
        return [
            'orderId' => $this->id?->getValue(),
            'customerId' => $this->customerId,
            'amount' => $this->amount,
            'eventSectionId' => $this->eventSectionId->getValue(),
        ];
    }
}