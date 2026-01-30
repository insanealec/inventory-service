<?php

use App\Actions\ShoppingList\Index;
use App\Models\ShoppingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('basic functionality - returning paginated lists', function () {
    // Create some test shopping lists
    ShoppingList::factory()->count(5)->create();
    
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result)->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class);
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(15);
});

test('search filter functionality - filtering by name', function () {
    // Create test shopping lists
    ShoppingList::factory()->create(['name' => 'Groceries']);
    ShoppingList::factory()->create(['name' => 'Electronics']);
    ShoppingList::factory()->create(['name' => 'Books']);
    
    // Test search for "Groceries"
    $request = new Request(['search' => 'Groceries']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Groceries');
    
    // Test search for "Book"
    $request = new Request(['search' => 'Book']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(1);
    expect($result->first()->name)->toBe('Books');
});

test('sorting functionality - sorting by name ascending', function () {
    // Create test shopping lists
    ShoppingList::factory()->create(['name' => 'Zebra']);
    ShoppingList::factory()->create(['name' => 'Apple']);
    ShoppingList::factory()->create(['name' => 'Banana']);
    
    // Test sorting by name ascending
    $request = new Request(['sort' => 'name', 'direction' => 'asc']);
    $result = (new Index())->handle($request);
    
    $names = $result->pluck('name')->toArray();
    expect($names)->toBe(['Apple', 'Banana', 'Zebra']);
});

test('sorting functionality - sorting by created_at descending', function () {
    // Create test shopping lists with different created_at times
    $list1 = ShoppingList::factory()->create(['name' => 'First']);
    sleep(1);
    $list2 = ShoppingList::factory()->create(['name' => 'Second']);
    sleep(1);
    $list3 = ShoppingList::factory()->create(['name' => 'Third']);
    
    // Test sorting by created_at descending (default)
    $request = new Request(['sort' => 'created_at', 'direction' => 'desc']);
    $result = (new Index())->handle($request);
    
    $names = $result->pluck('name')->toArray();
    expect($names)->toBe(['Third', 'Second', 'First']);
});

test('pagination functionality - custom per_page value', function () {
    // Create enough shopping lists for multiple pages
    ShoppingList::factory()->count(25)->create();
    
    // Test with custom per_page value
    $request = new Request(['per_page' => 10]);
    $result = (new Index())->handle($request);
    
    expect($result->perPage())->toBe(10);
    expect($result->total())->toBe(25);
    expect($result->lastPage())->toBe(3);
});

test('pagination functionality - default per_page value', function () {
    // Create some shopping lists
    ShoppingList::factory()->count(10)->create();
    
    // Test with default per_page value
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result->perPage())->toBe(15);
    expect($result->total())->toBe(10);
});

test('search filter functionality - empty search returns all lists', function () {
    // Create test shopping lists
    ShoppingList::factory()->create(['name' => 'Groceries']);
    ShoppingList::factory()->create(['name' => 'Electronics']);
    
    // Test with empty search
    $request = new Request(['search' => '']);
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(2);
    
    // Test with no search parameter
    $request = new Request();
    $result = (new Index())->handle($request);
    
    expect($result->total())->toBe(2);
});