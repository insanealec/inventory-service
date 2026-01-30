<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Actions\InventoryItem\Index;
use App\Actions\InventoryItem\Store;
use App\Actions\InventoryItem\Show;
use App\Actions\InventoryItem\Update;
use App\Actions\InventoryItem\Destroy;
use App\Actions\InventoryItem\LowStock;
use App\Actions\InventoryItem\Expiring;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the inventory items with optional search, filtering, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $items = Index::run($request);

        return response()->json($items);
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $item = Store::run($request);

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
        $item = Show::run($inventoryItem);

        return response()->json($item);
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        try {
            $item = Update::run($request, $inventoryItem);

            return response()->json($item);
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
        $result = Destroy::run($inventoryItem);

        return response()->json($result);
    }

    /**
     * Get inventory items that are below reorder point (low stock).
     */
    public function lowStock(Request $request): JsonResponse
    {
        $items = LowStock::run($request);

        return response()->json($items);
    }

    /**
     * Get inventory items that are expiring soon.
     */
    public function expiring(Request $request): JsonResponse
    {
        $items = Expiring::run($request);

        return response()->json($items);
    }
}
