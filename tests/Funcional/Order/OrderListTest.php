<?php

namespace Funcional\Order;

use Database\Factories\OrdersFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderListTest extends TestCase
{
    use RefreshDatabase;

    public function testListOrders(): void
    {

        OrdersFactory::new()->count(3)->create();

        $response = $this->get('/api/orders');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'status',
                'amount',
                'customer_id',
                'event_spot_id'
            ]
        ]);

        $response->assertJsonCount(3);
    }
}