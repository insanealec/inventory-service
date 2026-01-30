<?php

namespace App\Actions\ShoppingListItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingListItem;

class Destroy
{
    use AsAction;

    public function handle(ShoppingListItem $shoppingListItem)
    {
        $shoppingListItem->delete();

        return ['message' => 'Shopping list item deleted successfully'];
    }
}
