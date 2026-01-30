<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;

class Show
{
    use AsAction;

    public function handle(InventoryItem $inventoryItem)
    {
        return $inventoryItem->load(['stockLocation']);
    }
}
