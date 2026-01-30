<?php

namespace Database\Factories;

use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockLocationFactory extends Factory
{
    protected $model = StockLocation::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'short_name' => substr($this->faker->company(), 0, 3),
            'description' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}