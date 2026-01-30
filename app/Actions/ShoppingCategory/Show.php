<?php

namespace App\Actions\ShoppingCategory;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingCategory;

class Show
{
    use AsAction;

    public function handle(ShoppingCategory $shoppingCategory)
    {
        return $shoppingCategory;
    }
}
