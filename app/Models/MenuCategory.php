<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the menu items for this category.
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * Get active menu items for this category.
     */
    public function activeMenuItems()
    {
        return $this->hasMany(MenuItem::class)->where('is_available', true)->orderBy('sort_order');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the category icon based on name.
     */
    public function getIconAttribute()
    {
        return match(strtolower($this->name)) {
            'appetizers', 'starters' => '🥗',
            'main courses', 'mains', 'entrees' => '🍽️',
            'desserts', 'sweets' => '🍰',
            'beverages', 'drinks' => '🥤',
            'breakfast' => '🍳',
            'lunch' => '🍜',
            'dinner' => '🍛',
            'seafood' => '🦞',
            'pizza' => '🍕',
            'pasta' => '🍝',
            'salads' => '🥙',
            'soups' => '🍲',
            'grilled' => '🔥',
            'vegetarian' => '🥬',
            'cocktails' => '🍹',
            'coffee' => '☕',
            'wine' => '🍷',
            'beer' => '🍺',
            default => '🍴'
        };
    }
}
