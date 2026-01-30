<?php

namespace App\Actions\ShoppingCategory;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingCategory;
use Illuminate\Http\Request;

class Index
{
    use AsAction;

    public function handle(Request $request)
    {
        $query = ShoppingCategory::query();

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
        $categories = $query->paginate($perPage);

        return $categories;
    }
}
