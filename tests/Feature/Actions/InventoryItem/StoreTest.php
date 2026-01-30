<?php

use App\Actions\InventoryItem\Store;
use App\Models\InventoryItem;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use function Pest\Laravel\assertDatabaseHas;

test('store action creates inventory item with valid data', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $result = (new Store())->handle($request);
    
    expect($result)->toBeInstanceOf(InventoryItem::class);
    expect($result->name)->toEqual('Test Item');
    expect($result->sku)->toEqual('TEST-001');
    expect($result->stock_location_id)->toEqual($stockLocation->id);
    expect($result->quantity)->toEqual(10);
    expect($result->unit_price)->toEqual(19.99);
    
    assertDatabaseHas('inventory_items', [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
    ]);
});

test('store action validates required fields', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action validates unique sku constraint', function () {
    $stockLocation = StockLocation::factory()->create();
    
    // Create an inventory item with a specific SKU
    InventoryItem::factory()->create([
        'sku' => 'UNIQUE-SKU-123',
        'user_id' => 1,
    ]);
    
    // Try to create another item with the same SKU
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'UNIQUE-SKU-123', // Same SKU as existing item
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action validates stock location exists', function () {
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => 999, // Non-existent stock location ID
        'quantity' => 10,
        'unit_price' => 19.99,
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action validates numeric and integer fields', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 'invalid', // Should be integer
        'unit_price' => 'invalid', // Should be numeric
        'reorder_point' => 'invalid', // Should be integer
        'reorder_quantity' => 'invalid', // Should be integer
        'min_stock_level' => 'invalid', // Should be integer
        'max_stock_level' => 'invalid', // Should be integer
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action validates numeric fields are not negative', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => $stockLocation->id,
        'quantity' => -5, // Should not be negative
        'unit_price' => -10.50, // Should not be negative
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action validates reorder quantity is at least 1', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'name' => 'Test Item',
        'sku' => 'TEST-001',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
        'reorder_quantity' => 0, // Should be at least 1
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('store action works with nullable fields', function () {
    $stockLocation = StockLocation::factory()->create();
    
    $requestData = [
        'name' => 'Test Item',
        'stock_location_id' => $stockLocation->id,
        'quantity' => 10,
        'unit_price' => 19.99,
        'sku' => null, // Nullable
        'position' => null, // Nullable
        'description' => null, // Nullable
        'reorder_point' => null, // Nullable
        'reorder_quantity' => null, // Nullable
        'min_stock_level' => null, // Nullable
        'max_stock_level' => null, // Nullable
        'unit' => null, // Nullable
        'expiration_date' => null, // Nullable
        'user_id' => 1,
    ];
    
    $request = new Request($requestData);
    
    $result = (new Store())->handle($request);
    
    expect($result)->toBeInstanceOf(InventoryItem::class);
    expect($result->sku)->toBeNull();
    expect($result->position)->toBeNull();
    expect($result->description)->toBeNull();
    expect($result->reorder_point)->toBeNull();
    expect($result->reorder_quantity)->toBeNull();
    expect($result->min_stock_level)->toBeNull();
    expect($result->max_stock_level)->toBeNull();
    expect($result->unit)->toBeNull();
    expect($result->expiration_date)->toBeNull();
});