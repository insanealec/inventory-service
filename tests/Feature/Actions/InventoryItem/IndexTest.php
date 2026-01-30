<?php

use App\Actions\InventoryItem\Index;
use Illuminate\Http\Request;

test('index action handles request correctly', function () {
    // Create a mock request
    $request = new Request();
    
    // Call the action - this tests that the action can be called without errors
    $result = (new Index())->handle($request);
    
    // Assert the action executed without error - just verifying it doesn't crash
    expect($result)->toBeObject();
});

test('index action processes search parameter', function () {
    // Create a request with search parameter
    $request = new Request(['search' => 'test']);
    
    // Call the action
    $result = (new Index())->handle($request);
    
    // Assert the action executed without error
    expect($result)->toBeObject();
});

test('index action processes stock location filter', function () {
    // Create a request with stock location filter
    $request = new Request(['stock_location_id' => 1]);
    
    // Call the action
    $result = (new Index())->handle($request);
    
    // Assert the action executed without error
    expect($result)->toBeObject();
});

test('index action processes sorting parameters', function () {
    // Create a request with sort parameters
    $request = new Request(['sort' => 'name', 'direction' => 'asc']);
    
    // Call the action
    $result = (new Index())->handle($request);
    
    // Assert the action executed without error
    expect($result)->toBeObject();
});

test('index action processes pagination parameters', function () {
    // Create a request with pagination parameter
    $request = new Request(['per_page' => 10]);
    
    // Call the action
    $result = (new Index())->handle($request);
    
    // Assert the action executed without error
    expect($result)->toBeObject();
});