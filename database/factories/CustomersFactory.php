<?php

namespace Database\Factories;

use App\Models\CustomerModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Provider\pt_BR\Person;

class CustomersFactory extends Factory
{
    protected $model = CustomerModel::class;

    public function definition(): array
    {
        $this->faker->addProvider(new Person($this->faker));

        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'cpf' => $this->faker->cpf(),
        ];
    }
}