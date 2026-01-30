<?php

namespace App\Actions\ShoppingList;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingList;

class Show
{
    use AsAction;

    public function handle(ShoppingList $shoppingList)
    {
        return $shoppingList->load(['items']);
    }
}
