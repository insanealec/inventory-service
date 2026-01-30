<?php

namespace App\Actions\ShoppingList;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingList;
use Illuminate\Http\Request;

class Index
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = ShoppingList::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $lists = $query->paginate($perPage);

        return $lists;
    }
}
