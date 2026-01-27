<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the inventory items with optional search, filtering, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = InventoryItem::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply category filter
        if ($request->has('category_id') && $request->category_id) {
            // This would be implemented if we had a category relationship
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255|unique:inventory_items,sku',
                'stock_location_id' => 'required|exists:stock_locations,id',
                'position' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'reorder_point' => 'nullable|integer|min:0',
                'reorder_quantity' => 'nullable|integer|min:1',
                'min_stock_level' => 'nullable|integer|min:0',
                'max_stock_level' => 'nullable|integer|min:0',
                'unit_price' => 'required|numeric|min:0',
                'unit' => 'nullable|string|max:50',
                'expiration_date' => 'nullable|date',
            ]);

            $item = InventoryItem::create($validated);

            return response()->json($item, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem): JsonResponse
    {
        return response()->json($inventoryItem->load(['stockLocation']));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255|unique:inventory_items,sku,' . $inventoryItem->id,
                'stock_location_id' => 'required|exists:stock_locations,id',
                'position' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'reorder_point' => 'nullable|integer|min:0',
                'reorder_quantity' => 'nullable|integer|min:1',
                'min_stock_level' => 'nullable|integer|min:0',
                'max_stock_level' => 'nullable|integer|min:0',
                'unit_price' => 'required|numeric|min:0',
                'unit' => 'nullable|string|max:50',
                'expiration_date' => 'nullable|date',
            ]);

            $inventoryItem->update($validated);

            return response()->json($inventoryItem);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy(InventoryItem $inventoryItem): JsonResponse
    {
        $inventoryItem->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully'
        ]);
    }

    /**
     * Get inventory items that are below reorder point (low stock).
     */
    public function lowStock(Request $request): JsonResponse
    {
        $query = InventoryItem::whereColumn('quantity', '<', 'reorder_point')
            ->whereNotNull('reorder_point');

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'quantity');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $items = $query->get();

        return response()->json($items);
    }

    /**
     * Get inventory items that are expiring soon.
     */
    public function expiring(Request $request): JsonResponse
    {
        $query = InventoryItem::where('expiration_date', '<=', now()->addDays(7))
            ->where('expiration_date', '>=', now())
            ->whereNotNull('expiration_date');

        // Apply stock location filter
        if ($request->has('stock_location_id') && $request->stock_location_id) {
            $query->where('stock_location_id', $request->stock_location_id);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'expiration_date');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $items = $query->get();

        return response()->json($items);
    }
}
