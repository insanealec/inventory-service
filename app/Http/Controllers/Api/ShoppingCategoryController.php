<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ShoppingCategoryController extends Controller
{
    /**
     * Display a listing of the shopping categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ShoppingCategory::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('store_section', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);

        return response()->json($categories);
    }

    /**
     * Store a newly created shopping category in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:shopping_categories,name',
                'store_section' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'sort_order' => 'nullable|integer|min:0',
                'user_id' => 'required|exists:users,id',
            ]);

            $category = ShoppingCategory::create($validated);

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
        return response()->json($shoppingCategory->load(['user']));
    }

    /**
     * Update the specified shopping category in storage.
     */
    public function update(Request $request, ShoppingCategory $shoppingCategory): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:shopping_categories,name,' . $shoppingCategory->id,
                'store_section' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'sort_order' => 'nullable|integer|min:0',
                'user_id' => 'required|exists:users,id',
            ]);

            $shoppingCategory->update($validated);

            return response()->json($shoppingCategory);
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

        $shoppingCategory->delete();

        return response()->json([
            'message' => 'Shopping category deleted successfully'
        ]);
    }
}
