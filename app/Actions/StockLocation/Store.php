<?php

namespace App\Actions\StockLocation;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Store
{
    use AsAction;

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stock_locations,name',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $location = StockLocation::create($validated);

        return $location;
    }
}
