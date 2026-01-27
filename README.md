# Inventory Management System

This is a comprehensive inventory management system built with Laravel and Vue.js that enables users to track inventory items, manage stock locations, and create shopping lists.

## Features

- Manage inventory items with detailed properties
- Track stock levels and set reorder points
- Assign items to specific storage locations
- Monitor expiring items
- Generate shopping lists from inventory needs
- User authentication and profile management

## System Architecture

The system follows a modern MVC architecture with:

- Laravel 12 backend with Inertia.js frontend
- SQLite database (configurable for production)
- Sanctum for API authentication
- RESTful API endpoints

## Getting Started

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure your environment
5. Run `php artisan migrate`
6. Run `php artisan serve` to start the development server

## API Endpoints

### Inventory Items

- `GET /api/inventory-items` - List all inventory items
- `POST /api/inventory-items` - Create a new inventory item
- `GET /api/inventory-items/:id` - Retrieve a specific inventory item
- `PUT /api/inventory-items/:id` - Update an inventory item
- `DELETE /api/inventory-items/:id` - Delete an inventory item

### Shopping Lists

- `POST /api/shopping-lists/from-inventory` - Create shopping list from inventory needs

## Security

The system implements:

- Laravel Sanctum for API authentication
- Laravel Fortify for authentication services
- CSRF protection
- Input validation

## Development Guidelines

See [AGENTS.md](AGENTS.md) for development guidelines and coding standards.
