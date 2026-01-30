<?php

namespace App\Actions\ShoppingCategory;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Store
{
    use AsAction;

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shopping_categories,name',
            'store_section' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        $category = ShoppingCategory::create($validated);

        return $category;
    }
}
