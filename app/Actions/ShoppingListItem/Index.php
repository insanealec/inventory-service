<?php

namespace App\Actions\ShoppingListItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;

class Index
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = ShoppingListItem::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply shopping list filter
        if ($request->has('shopping_list_id') && $request->shopping_list_id) {
            $query->where('shopping_list_id', $request->shopping_list_id);
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
