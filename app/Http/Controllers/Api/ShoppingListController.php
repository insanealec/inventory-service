<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the shopping lists.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ShoppingList::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        // Apply completion filter
        if ($request->has('completed') && $request->completed !== null) {
            $query->where('is_completed', $request->completed);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $lists = $query->paginate($perPage);

        return response()->json($lists);
    }

    /**
     * Store a newly created shopping list in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'is_completed' => 'boolean',
                'shopping_date' => 'nullable|date',
                'user_id' => 'required|exists:users,id',
            ]);

            $list = ShoppingList::create($validated);

            return response()->json($list, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified shopping list.
     */
    public function show(ShoppingList $shoppingList): JsonResponse
    {
        return response()->json($shoppingList->load(['user', 'shoppingListItems']));
    }

    /**
     * Update the specified shopping list in storage.
     */
    public function update(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'is_completed' => 'boolean',
                'shopping_date' => 'nullable|date',
                'user_id' => 'required|exists:users,id',
            ]);

            $shoppingList->update($validated);

            return response()->json($shoppingList);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified shopping list from storage.
     */
    public function destroy(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->delete();

        return response()->json([
            'message' => 'Shopping list deleted successfully'
        ]);
    }

    /**
     * Create a shopping list from current inventory needs.
     */
    public function fromInventory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'user_id' => 'required|exists:users,id',
            ]);

            // This would be implemented with logic to analyze inventory items
            // that are below reorder point and generate shopping list items
            // For now, we'll just create a basic list
            
            $list = ShoppingList::create($validated);

            return response()->json($list, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
