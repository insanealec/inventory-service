<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'sku' => $this->faker->word(),
            'stock_location_id' => null,
            'position' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'reorder_point' => $this->faker->numberBetween(1, 100),
            'reorder_quantity' => $this->faker->numberBetween(1, 100),
            'min_stock_level' => $this->faker->numberBetween(1, 1000),
            'max_stock_level' => $this->faker->numberBetween(1, 1000),
            'unit_price' => $this->faker->randomFloat(2, 1, 1000),
            'unit' => 'pcs',
            'expiration_date' => $this->faker->date(),
            'user_id' => 1,
        ];
    }
}