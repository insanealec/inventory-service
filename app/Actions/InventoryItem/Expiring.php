<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class Expiring
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = InventoryItem::where('expiration_date', '<=', now()->addDays(7))
            ->where('expiration_date', '>=', now())
            ->whereNotNull('expiration_date');

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'expiration_date');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $items = $query->get();

        return $items;
    }
}
