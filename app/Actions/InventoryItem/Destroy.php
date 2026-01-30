<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;

class Destroy
{
    use AsAction;

    public function handle(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();

        return ['message' => 'Inventory item deleted successfully'];
    }
}
