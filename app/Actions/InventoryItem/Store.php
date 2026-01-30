<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Store
{
    use AsAction;

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:inventory_items,sku',
            'stock_location_id' => 'required|exists:stock_locations,id',
            'position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:1',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date',
        ]);

        $item = InventoryItem::create($validated);

        return $item;
    }
}
