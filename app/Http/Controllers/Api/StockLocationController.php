<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Actions\StockLocation\Index;
use App\Actions\StockLocation\Store;
use App\Actions\StockLocation\Show;
use App\Actions\StockLocation\Update;
use App\Actions\StockLocation\Destroy;

class StockLocationController extends Controller
{
    /**
     * Display a listing of the stock locations.
     */
    public function index(Request $request): JsonResponse
    {
        $locations = Index::run($request);

        return response()->json($locations);
    }

    /**
     * Store a newly created stock location in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $location = Store::run($request);

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
        $location = Show::run($stockLocation);

        return response()->json($location);
    }

    /**
     * Update the specified stock location in storage.
     */
    public function update(Request $request, StockLocation $stockLocation): JsonResponse
    {
        try {
            $location = Update::run($request, $stockLocation);

            return response()->json($location);
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

        $result = Destroy::run($stockLocation);

        return response()->json($result);
    }
}
