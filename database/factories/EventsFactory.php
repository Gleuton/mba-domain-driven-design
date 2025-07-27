<?php

namespace Database\Factories;

use App\Models\EventModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventsFactory extends Factory
{
    protected $model = EventModel::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_published' => $this->faker->boolean(),
            'total_spots' => $this->faker->numberBetween(10, 100),
            'total_spots_reserved' => $this->faker->numberBetween(0, 50),
            'partner_id' => PartnerFactory::new()->create()->id,
        ];
    }
}