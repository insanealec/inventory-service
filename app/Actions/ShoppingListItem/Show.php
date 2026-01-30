<?php

namespace App\Actions\ShoppingListItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingListItem;

class Show
{
    use AsAction;

    public function handle(ShoppingListItem $shoppingListItem)
    {
        return $shoppingListItem->load(['shoppingList', 'category']);
    }
}
