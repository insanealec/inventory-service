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
use App\Actions\ShoppingListItem\Index;
use App\Actions\ShoppingListItem\Store;
use App\Actions\ShoppingListItem\Show;
use App\Actions\ShoppingListItem\Update;
use App\Actions\ShoppingListItem\Destroy;

class ShoppingListItemController extends Controller
{
    /**
     * Display a listing of the shopping list items.
     */
    public function index(Request $request): JsonResponse
    {
        $items = Index::run($request);

        return response()->json($items);
    }

    /**
     * Store a newly created shopping list item in storage.
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
        $item = Show::run($shoppingListItem);

        return response()->json($item);
    }

    /**
     * Update the specified shopping list item in storage.
     */
    public function update(Request $request, ShoppingListItem $shoppingListItem): JsonResponse
    {
        try {
            $item = Update::run($request, $shoppingListItem);

            return response()->json($item);
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
        $result = Destroy::run($shoppingListItem);

        return response()->json($result);
    }
}
