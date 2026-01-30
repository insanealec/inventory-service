<?php

use App\Actions\ShoppingCategory\Index;
use App\Models\ShoppingCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('basic functionality - returning paginated categories', function () {
    // Create some test categories
    ShoppingCategory::factory()->count(5)->create();
    
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result)->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class);
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(15);
});

test('search filter functionality - filtering by name', function () {
    // Create test categories
    ShoppingCategory::factory()->create(['name' => 'Electronics']);
    ShoppingCategory::factory()->create(['name' => 'Clothing']);
    ShoppingCategory::factory()->create(['name' => 'Books']);
    
    // Test search for "Electronics"
    $request = new Request(['search' => 'Electronics']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Electronics');
    
    // Test search for "Clo"
    $request = new Request(['search' => 'Clo']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Clothing');
});

test('sorting functionality - sorting by name ascending', function () {
    // Create test categories
    ShoppingCategory::factory()->create(['name' => 'Zebra']);
    ShoppingCategory::factory()->create(['name' => 'Apple']);
    ShoppingCategory::factory()->create(['name' => 'Banana']);
    
    // Test sorting by name ascending
    $request = new Request(['sort' => 'name', 'direction' => 'asc']);
    $result = (new Index())->handle($request);
    
    $names = $result->pluck('name')->toArray();
    expect($names)->toBe(['Apple', 'Banana', 'Zebra']);
});

test('sorting functionality - sorting by created_at descending', function () {
    // Create test categories with different created_at times
    $category1 = ShoppingCategory::factory()->create(['name' => 'First']);
    sleep(1);
    $category2 = ShoppingCategory::factory()->create(['name' => 'Second']);
    sleep(1);
    $category3 = ShoppingCategory::factory()->create(['name' => 'Third']);
    
    // Test sorting by created_at descending (default)
    $request = new Request(['sort' => 'created_at', 'direction' => 'desc']);
    $result = (new Index())->handle($request);
    
    $names = $result->pluck('name')->toArray();
    expect($names)->toBe(['Third', 'Second', 'First']);
});

test('pagination functionality - custom per_page value', function () {
    // Create enough categories for multiple pages
    ShoppingCategory::factory()->count(25)->create();
    
    // Test with custom per_page value
    $request = new Request(['per_page' => 10]);
    $result = (new Index())->handle($request);
    
    expect($result->perPage())->toBe(10);
    expect($result->total())->toBe(25);
    expect($result->lastPage())->toBe(3);
});

test('pagination functionality - default per_page value', function () {
    // Create some categories
    ShoppingCategory::factory()->count(10)->create();
    
    // Test with default per_page value
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result->perPage())->toBe(15);
    expect($result->total())->toBe(10);
});

test('search filter functionality - empty search returns all categories', function () {
    // Create test categories
    ShoppingCategory::factory()->create(['name' => 'Electronics']);
    ShoppingCategory::factory()->create(['name' => 'Clothing']);
    
    // Test with empty search
    $request = new Request(['search' => '']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(2);
    
    // Test with no search parameter
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(2);
});