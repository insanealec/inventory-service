<?php

namespace App\Actions\ShoppingCategory;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingCategory;

class Destroy
{
    use AsAction;

    public function handle(ShoppingCategory $shoppingCategory)
    {
        // Check if category has shopping list items before deleting
        if ($shoppingCategory->shoppingListItems()->exists()) {
            throw new \Exception('Cannot delete category that has shopping list items assigned to it');
        }

        $shoppingCategory->delete();

        return ['message' => 'Shopping category deleted successfully'];
    }
}
