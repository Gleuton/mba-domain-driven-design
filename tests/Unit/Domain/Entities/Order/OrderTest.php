<?php

namespace Tests\Unit\Domain\Entities\Order;

use App\Events\Domain\Entities\Customer\CustomerId;
use App\Events\Domain\Entities\EventSpot\EventSpotId;
use App\Events\Domain\Entities\Order\Order;
use App\Events\Domain\Entities\Order\OrderId;
use App\Events\Domain\Entities\Order\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testCreateOrder(): void
    {
        $orderId = new OrderId();
        $customerId = new CustomerId();
        $eventSpotId = new EventSpotId();
        
        $orderData = [
            'orderId' => $orderId,
            'customerId' => $customerId,
            'amount' => 100.0,
            'eventSpotId' => $eventSpotId
        ];

        $order = Order::create($orderData);

        $serialized = $order->toArray();
        
        $this->assertEquals($orderId, $serialized['id']);
        $this->assertEquals($customerId, $serialized['customer_id']);
        $this->assertEquals(100.0, $serialized['amount']);
        $this->assertEquals($eventSpotId, $serialized['event_spot_id']);
        $this->assertEquals(OrderStatus::PENDING->value, $serialized['status']);
    }

    public function testCreateOrderWithoutOrderId(): void
    {
        $customerId = new CustomerId();
        $eventSpotId = new EventSpotId();
        
        $orderData = [
            'customerId' => $customerId,
            'amount' => 100.0,
            'eventSpotId' => $eventSpotId
        ];

        $order = Order::create($orderData);
        
        $serialized = $order->toArray();
        
        $this->assertNotEmpty($serialized['id']);
        $this->assertEquals($customerId, $serialized['customer_id']);
        $this->assertEquals(100.0, $serialized['amount']);
        $this->assertEquals($eventSpotId, $serialized['event_spot_id']);
        $this->assertEquals(OrderStatus::PENDING->value, $serialized['status']);
    }

    public function testCreateOrderWithoutAmount(): void
    {
        $customerId = new CustomerId();
        $eventSpotId = new EventSpotId();
        
        $orderData = [
            'customerId' => $customerId,
            'eventSpotId' => $eventSpotId
        ];

        $order = Order::create($orderData);
        
        $serialized = $order->toArray();
        
        $this->assertNotEmpty($serialized['id']);
        $this->assertEquals($customerId, $serialized['customer_id']);
        $this->assertEquals(0.0, $serialized['amount']);
        $this->assertEquals($eventSpotId, $serialized['event_spot_id']);
        $this->assertEquals(OrderStatus::PENDING->value, $serialized['status']);
    }
}