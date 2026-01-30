<?php

use App\Actions\InventoryItem\Show;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('basic functionality - returning an inventory item with loaded relationships', function () {
    $stockLocation = StockLocation::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'stock_location_id' => $stockLocation->id,
    ]);

    $result = (new Show())->handle($inventoryItem);

    expect($result)->toBeInstanceOf(InventoryItem::class);
    expect($result->id)->toBe($inventoryItem->id);
    expect($result->stockLocation)->toBeInstanceOf(StockLocation::class);
});

test('relationship loading - verifying that the stockLocation relationship is loaded', function () {
    $stockLocation = StockLocation::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'stock_location_id' => $stockLocation->id,
    ]);

    $result = (new Show())->handle($inventoryItem);

    expect($result->stockLocation)->toBeInstanceOf(StockLocation::class);
    expect($result->stockLocation->id)->toBe($stockLocation->id);
});

test('data integrity - checking that the returned item matches the input', function () {
    $stockLocation = StockLocation::factory()->create([
        'name' => 'Main Warehouse',
        'address' => '123 Main St',
    ]);
    
    $inventoryItem = InventoryItem::factory()->create([
        'name' => 'Product A',
        'description' => 'A great product',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 100,
        'unit_price' => 29.99,
    ]);

    $result = (new Show())->handle($inventoryItem);

    expect($result->id)->toBe($inventoryItem->id);
    expect($result->name)->toBe($inventoryItem->name);
    expect($result->description)->toBe($inventoryItem->description);
    expect($result->quantity)->toBe($inventoryItem->quantity);
    expect($result->unit_price)->toBe($inventoryItem->unit_price);
    expect($result->stock_location_id)->toBe($inventoryItem->stock_location_id);
    expect($result->stockLocation->id)->toBe($stockLocation->id);
    expect($result->stockLocation->name)->toBe($stockLocation->name);
    expect($result->stockLocation->address)->toBe($stockLocation->address);
});