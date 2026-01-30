<?php

use App\Actions\InventoryItem\Update;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('basic functionality - updating an inventory item with valid data', function () {
    $stockLocation = StockLocation::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);

    $requestData = [
        'name' => 'Updated Product Name',
        'sku' => 'NEW-SKU-123',
        'stock_location_id' => $stockLocation->id,
        'position' => 'A123',
        'description' => 'Updated product description',
        'quantity' => 50,
        'reorder_point' => 10,
        'reorder_quantity' => 25,
        'min_stock_level' => 5,
        'max_stock_level' => 100,
        'unit_price' => 29.99,
        'unit' => 'pcs',
        'expiration_date' => '2025-12-31',
    ];

    $result = (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem
    );

    expect($result)->toBeInstanceOf(InventoryItem::class);
    expect($result->name)->toBe($requestData['name']);
    expect($result->sku)->toBe($requestData['sku']);
    expect($result->position)->toBe($requestData['position']);
    expect($result->description)->toBe($requestData['description']);
    expect($result->quantity)->toBe($requestData['quantity']);
    expect($result->reorder_point)->toBe($requestData['reorder_point']);
    expect($result->reorder_quantity)->toBe($requestData['reorder_quantity']);
    expect($result->min_stock_level)->toBe($requestData['min_stock_level']);
    expect($result->max_stock_level)->toBe($requestData['max_stock_level']);
    expect($result->unit_price)->toBe($requestData['unit_price']);
    expect($result->unit)->toBe($requestData['unit']);
    expect($result->expiration_date)->toBe($requestData['expiration_date']);
});

test('validation - ensuring required fields are validated', function () {
    $stockLocation = StockLocation::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);

    $requestData = [
        'name' => '',
        'stock_location_id' => null,
        'quantity' => 'invalid',
        'unit_price' => 'invalid',
    ];

    $this->expectException(ValidationException::class);

    (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem
    );
});

test('unique sku constraint - checking that SKUs must be unique (excluding the current item)', function () {
    $stockLocation = StockLocation::factory()->create();
    
    // Create two inventory items with same SKU
    $inventoryItem1 = InventoryItem::factory()->create([
        'sku' => 'SAME-SKU-123',
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);
    
    $inventoryItem2 = InventoryItem::factory()->create([
        'sku' => 'SAME-SKU-123',
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);

    // Try to update inventoryItem1 to have the same SKU as inventoryItem2 (should fail)
    $requestData = [
        'name' => 'Updated Product',
        'sku' => 'SAME-SKU-123', // Same SKU as inventoryItem2
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 29.99,
        'user_id' => 1,
    ];

    $this->expectException(ValidationException::class);

    (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem1
    );
});

test('unique sku constraint - checking that updating with own SKU succeeds', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $inventoryItem = InventoryItem::factory()->create([
        'sku' => 'OWN-SKU-123',
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);

    // Update the same item with its own SKU (should succeed)
    $requestData = [
        'name' => 'Updated Product',
        'sku' => 'OWN-SKU-123', // Same SKU as the item itself
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 29.99,
        'user_id' => 1,
    ];

    $result = (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem
    );

    expect($result->sku)->toBe('OWN-SKU-123');
});

test('stock location existence - checking that stock_location_id references existing location', function () {
    $inventoryItem = InventoryItem::factory()->create([
        'user_id' => 1,
    ]);

    $requestData = [
        'name' => 'Product Name',
        'stock_location_id' => 999, // Non-existent location ID
        'quantity' => 10,
        'unit_price' => 29.99,
        'user_id' => 1,
    ];

    $this->expectException(ValidationException::class);

    (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem
    );
});

test('numeric and integer validations - ensuring proper data types for numeric fields', function () {
    $stockLocation = StockLocation::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'stock_location_id' => $stockLocation->id,
        'user_id' => 1,
    ]);

    $requestData = [
        'name' => 'Product Name',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 'not-integer',
        'reorder_point' => 'not-integer',
        'reorder_quantity' => 'not-integer',
        'min_stock_level' => 'not-integer',
        'max_stock_level' => 'not-integer',
        'unit_price' => 'not-numeric',
        'user_id' => 1,
    ];

    $this->expectException(ValidationException::class);

    (new Update())->handle(
        new \Illuminate\Http\Request([], $requestData),
        $inventoryItem
    );
});