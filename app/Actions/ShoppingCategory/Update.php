<?php

namespace App\Actions\ShoppingCategory;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Update
{
    use AsAction;

    public function handle(Request $request, ShoppingCategory $shoppingCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shopping_categories,name,' . $shoppingCategory->id,
            'description' => 'nullable|string',
        ]);

        $shoppingCategory->update($validated);

        return $shoppingCategory;
    }
}
