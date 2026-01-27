<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingListItem;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class ShoppingListItemController extends Controller
{
    /**
     * Display a listing of the shopping list items.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ShoppingListItem::query();
        
        // Filter by shopping list ID if provided
        if ($request->has('shopping_list_id') && $request->shopping_list_id) {
            $query->where('shopping_list_id', $request->shopping_list_id);
        }
        
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
        $items = $query->paginate($perPage);
        
        return response()->json($items);
    }

    /**
     * Store a newly created shopping list item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'shopping_list_id' => 'required|exists:shopping_lists,id',
                'name' => 'required|string|max:255',
                'quantity' => 'nullable|integer|min:0',
                'unit' => 'nullable|string|max:50',
                'is_completed' => 'boolean',
                'category_id' => 'nullable|exists:shopping_categories,id',
                'notes' => 'nullable|string',
                'estimated_price' => 'nullable|numeric|min:0',
                'priority' => 'nullable|integer|min:0|max:10',
                'inventory_item_id' => 'nullable|exists:inventory_items,id',
                'sort_order' => 'nullable|integer',
            ]);
            
            // Verify user owns the shopping list before creating item
            $shoppingList = ShoppingList::findOrFail($request->shopping_list_id);
            if ($shoppingList->user_id !== Auth::id()) {
                throw new AuthorizationException('You do not have permission to add items to this shopping list.');
            }
            
            $item = ShoppingListItem::create($validated);
            
            return response()->json($item, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shopping list not found'
            ], 404);
        }
    }

    /**
     * Display the specified shopping list item.
     */
    public function show(ShoppingListItem $shoppingListItem): JsonResponse
    {
        // Verify ownership
        $shoppingList = $shoppingListItem->shoppingList;
        if ($shoppingList->user_id !== Auth::id()) {
            throw new AuthorizationException('You do not have permission to view this shopping list item.');
        }
        
        return response()->json($shoppingListItem->load(['shoppingList', 'category', 'inventoryItem']));
    }

    /**
     * Update the specified shopping list item in storage.
     */
    public function update(Request $request, ShoppingListItem $shoppingListItem): JsonResponse
    {
        try {
            // Verify ownership
            $shoppingList = $shoppingListItem->shoppingList;
            if ($shoppingList->user_id !== Auth::id()) {
                throw new AuthorizationException('You do not have permission to update this shopping list item.');
            }
            
            $validated = $request->validate([
                'shopping_list_id' => 'required|exists:shopping_lists,id',
                'name' => 'required|string|max:255',
                'quantity' => 'nullable|integer|min:0',
                'unit' => 'nullable|string|max:50',
                'is_completed' => 'boolean',
                'category_id' => 'nullable|exists:shopping_categories,id',
                'notes' => 'nullable|string',
                'estimated_price' => 'nullable|numeric|min:0',
                'priority' => 'nullable|integer|min:0|max:10',
                'inventory_item_id' => 'nullable|exists:inventory_items,id',
                'sort_order' => 'nullable|integer',
            ]);
            
            // Verify user owns the shopping list
            $targetShoppingList = ShoppingList::findOrFail($request->shopping_list_id);
            if ($targetShoppingList->user_id !== Auth::id()) {
                throw new AuthorizationException('You do not have permission to move this item to another shopping list.');
            }
            
            $shoppingListItem->update($validated);
            
            return response()->json($shoppingListItem);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Shopping list not found'
            ], 404);
        }
    }

    /**
     * Remove the specified shopping list item from storage.
     */
    public function destroy(ShoppingListItem $shoppingListItem): JsonResponse
    {
        try {
            // Verify ownership
            $shoppingList = $shoppingListItem->shoppingList;
            if ($shoppingList->user_id !== Auth::id()) {
                throw new AuthorizationException('You do not have permission to delete this shopping list item.');
            }
            
            $shoppingListItem->delete();
            
            return response()->json([
                'message' => 'Shopping list item deleted successfully'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403);
        }
    }
}
