<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StockLocationController extends Controller
{
    /**
     * Display a listing of the stock locations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockLocation::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $locations = $query->paginate($perPage);

        return response()->json($locations);
    }

    /**
     * Store a newly created stock location in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:stock_locations,name',
                'short_name' => 'required|string|max:50',
                'description' => 'nullable|string',
                'user_id' => 'required|exists:users,id',
            ]);

            $location = StockLocation::create($validated);

            return response()->json($location, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified stock location.
     */
    public function show(StockLocation $stockLocation): JsonResponse
    {
        return response()->json($stockLocation->load(['user', 'inventoryItems']));
    }

    /**
     * Update the specified stock location in storage.
     */
    public function update(Request $request, StockLocation $stockLocation): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:stock_locations,name,' . $stockLocation->id,
                'short_name' => 'required|string|max:50',
                'description' => 'nullable|string',
                'user_id' => 'required|exists:users,id',
            ]);

            $stockLocation->update($validated);

            return response()->json($stockLocation);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified stock location from storage.
     */
    public function destroy(StockLocation $stockLocation): JsonResponse
    {
        // Check if location has inventory items before deleting
        if ($stockLocation->inventoryItems()->exists()) {
            return response()->json([
                'message' => 'Cannot delete location that has inventory items assigned to it'
            ], 400);
        }

        $stockLocation->delete();

        return response()->json([
            'message' => 'Stock location deleted successfully'
        ]);
    }
}
