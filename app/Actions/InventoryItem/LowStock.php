<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class LowStock
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = InventoryItem::whereColumn('quantity', '<', 'reorder_point')
            ->whereNotNull('reorder_point');

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'quantity');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $items = $query->get();

        return $items;
    }
}
