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
            'description' => 'nullable|string',
        ]);

        $category = ShoppingCategory::create($validated);

        return $category;
    }
}
