<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'stock_location_id',
        'position',
        'description',
        'quantity',
        'reorder_point',
        'reorder_quantity',
        'min_stock_level',
        'max_stock_level',
        'unit_price',
        'unit',
        'expiration_date',
    ];

    public function stockLocation()
    {
        return $this->belongsTo(StockLocation::class);
    }

    public function shoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
