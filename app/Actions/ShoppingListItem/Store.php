<?php

namespace App\Actions\ShoppingListItem;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Store
{
    use AsAction;

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shopping_list_id' => 'required|exists:shopping_lists,id',
            'quantity' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        $item = ShoppingListItem::create($validated);

        return $item;
    }
}
