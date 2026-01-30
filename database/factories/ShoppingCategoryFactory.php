<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingCategory>
 */
class ShoppingCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'store_section' => $this->faker->word(),
            'color' => $this->faker->hexColor(),
            'sort_order' => $this->faker->randomNumber(2),
            'user_id' => User::factory(), // Create a user or reference existing one
        ];
    }
}
