<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Inventory Items Routes
Route::apiResource('inventory-items', App\Http\Controllers\Api\InventoryItemController::class);

// Stock Locations Routes
Route::apiResource('stock-locations', App\Http\Controllers\Api\StockLocationController::class);

// Shopping Lists Routes
Route::apiResource('shopping-lists', App\Http\Controllers\Api\ShoppingListController::class);
Route::post('shopping-lists/from-inventory', [App\Http\Controllers\Api\ShoppingListController::class, 'fromInventory']);

// Shopping Categories Routes
Route::apiResource('shopping-categories', App\Http\Controllers\Api\ShoppingCategoryController::class);

// Shopping List Items Routes
Route::apiResource('shopping-list-items', App\Http\Controllers\Api\ShoppingListItemController::class);
