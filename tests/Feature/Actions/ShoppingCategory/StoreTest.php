<?php

use App\Actions\ShoppingCategory\Store;
use App\Models\ShoppingCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('basic functionality - creating a shopping category with valid data', function () {
    $user = User::factory()->create();
    
    $request = new Request([
        'name' => 'Electronics',
        'store_section' => 'Electronics',
        'color' => '#FF0000',
        'sort_order' => 1,
    ]);
    
    // Mock the authenticated user
    Auth::login($user);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $category = (new Store())->handle($request);
    
    expect($category)->toBeInstanceOf(ShoppingCategory::class);
    expect($category->name)->toBe('Electronics');
    expect($category->store_section)->toBe('Electronics');
    expect($category->color)->toBe('#FF0000');
    expect($category->sort_order)->toBe(1);
    expect($category->id)->not->toBeNull();
    expect($category->user_id)->toBe($user->id);
});

test('validation - ensuring required fields are validated', function () {
    $user = User::factory()->create();
    
    $request = new Request([
        'name' => '',
        'store_section' => 'Electronics',
    ]);
    
    // Mock the authenticated user
    Auth::login($user);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});

test('unique name constraint - checking that names must be unique', function () {
    $user = User::factory()->create();
    
    // Create a category first
    ShoppingCategory::factory()->create(['name' => 'Electronics', 'user_id' => $user->id]);
    
    // Try to create another category with the same name
    $request = new Request([
        'name' => 'Electronics',
        'store_section' => 'Electronics',
    ]);
    
    // Mock the authenticated user
    Auth::login($user);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $this->expectException(ValidationException::class);
    
    (new Store())->handle($request);
});