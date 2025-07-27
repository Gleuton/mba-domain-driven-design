<?php

namespace Database\Factories;

use App\Models\OrderModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrdersFactory extends Factory
{
    protected $model = OrderModel::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Cancelled']),
            'event_spot_id' => EventSpotsFactory::new()->create()->id,
            'customer_id' => CustomersFactory::new()->create()->id,
        ];
    }
}