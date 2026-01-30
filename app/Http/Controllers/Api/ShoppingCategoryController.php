<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Actions\ShoppingCategory\Index;
use App\Actions\ShoppingCategory\Store;
use App\Actions\ShoppingCategory\Show;
use App\Actions\ShoppingCategory\Update;
use App\Actions\ShoppingCategory\Destroy;

class ShoppingCategoryController extends Controller
{
    /**
     * Display a listing of the shopping categories.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Index::run($request);

        return response()->json($categories);
    }

    /**
     * Store a newly created shopping category in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $category = Store::run($request);

            return response()->json($category, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified shopping category.
     */
    public function show(ShoppingCategory $shoppingCategory): JsonResponse
    {
        $category = Show::run($shoppingCategory);

        return response()->json($category);
    }

    /**
     * Update the specified shopping category in storage.
     */
    public function update(Request $request, ShoppingCategory $shoppingCategory): JsonResponse
    {
        try {
            $category = Update::run($request, $shoppingCategory);

            return response()->json($category);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified shopping category from storage.
     */
    public function destroy(ShoppingCategory $shoppingCategory): JsonResponse
    {
        // Check if category has shopping list items before deleting
        if ($shoppingCategory->shoppingListItems()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category that has shopping list items assigned to it'
            ], 400);
        }

        $result = Destroy::run($shoppingCategory);

        return response()->json($result);
    }
}
