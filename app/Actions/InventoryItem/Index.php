<?php

namespace App\Actions\InventoryItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class Index
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = InventoryItem::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);

        return $items;
    }
}
