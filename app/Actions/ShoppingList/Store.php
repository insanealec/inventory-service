<?php

namespace App\Actions\ShoppingList;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Store
{
    use AsAction;

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shopping_lists,name',
            'description' => 'nullable|string',
        ]);

        $list = ShoppingList::create($validated);

        return $list;
    }
}
