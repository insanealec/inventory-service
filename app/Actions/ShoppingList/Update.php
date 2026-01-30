<?php

namespace App\Actions\ShoppingList;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Update
{
    use AsAction;

    public function handle(Request $request, ShoppingList $shoppingList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shopping_lists,name,' . $shoppingList->id,
            'description' => 'nullable|string',
        ]);

        $shoppingList->update($validated);

        return $shoppingList;
    }
}
