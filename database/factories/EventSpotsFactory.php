<?php

namespace Database\Factories;

use App\Models\EventSpotModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventSpotsFactory extends Factory
{
    protected $model = EventSpotModel::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'location' => $this->faker->unique()->word(),
            'is_reserved' => $this->faker->boolean(),
            'is_published' => $this->faker->boolean(),
            'event_section_id' => EventSectionsFactory::new()->create()->id,
        ];
    }
}