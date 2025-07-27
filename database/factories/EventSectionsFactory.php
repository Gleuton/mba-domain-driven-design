<?php

namespace Database\Factories;

use App\Models\EventSectionModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventSectionsFactory extends Factory
{
    protected $model = EventSectionModel::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'is_published' => $this->faker->boolean(),
            'total_spots' => $this->faker->numberBetween(10, 100),
            'total_spots_reserved' => $this->faker->numberBetween(0, 50),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'event_id' => EventsFactory::new()->create()->id,
        ];
    }
}