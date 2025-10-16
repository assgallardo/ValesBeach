<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'food_order_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the food order that owns this item.
     */
    public function foodOrder()
    {
        return $this->belongsTo(FoodOrder::class);
    }

    /**
     * Get the menu item associated with this order item.
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format((float)$this->unit_price, 2);
    }

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format((float)$this->total_price, 2);
    }

    /**
     * Get the total amount for this order item.
     */
    public function getTotalAttribute()
    {
        return $this->total_price;
    }

    /**
     * Get the item name from the associated menu item.
     */
    public function getItemNameAttribute()
    {
        return $this->menuItem ? $this->menuItem->name : 'Unknown Item';
    }

    /**
     * Get the item description from the associated menu item.
     */
    public function getItemDescriptionAttribute()
    {
        return $this->menuItem ? $this->menuItem->description : '';
    }

    /**
     * Get the item image from the associated menu item.
     */
    public function getItemImageAttribute()
    {
        return $this->menuItem ? $this->menuItem->image : null;
    }

    /**
     * Get dietary badges from the associated menu item.
     */
    public function getDietaryBadgesAttribute()
    {
        return $this->menuItem ? $this->menuItem->dietary_badges : [];
    }

    /**
     * Check if this order item has special instructions.
     */
    public function hasSpecialInstructions()
    {
        return !empty($this->special_instructions);
    }
}
