<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ShoppingList;
use App\Models\InventoryItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingListItem>
 */
class ShoppingListItemFactory extends Factory
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
            'quantity' => $this->faker->randomNumber(2),
            'unit' => $this->faker->word(),
            'notes' => $this->faker->optional()->sentence(),
            'shopping_list_id' => ShoppingList::factory(),
            'inventory_item_id' => InventoryItem::factory(),
        ];
    }
}