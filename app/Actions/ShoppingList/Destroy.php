<?php

namespace App\Actions\ShoppingList;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingList;

class Destroy
{
    use AsAction;

    public function handle(ShoppingList $shoppingList)
    {
        $shoppingList->delete();

        return ['message' => 'Shopping list deleted successfully'];
    }
}
