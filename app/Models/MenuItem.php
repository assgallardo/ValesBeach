<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'menu_category_id',
        'name',
        'description',
        'price',
        'image',
        'ingredients',
        'allergens',
        'preparation_time',
        'calories',
        'is_available',
        'is_featured',
        'is_spicy',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'preparation_time' => 'integer',
        'calories' => 'integer',
        'sort_order' => 'integer',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_spicy' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
    ];

    /**
     * Get the menu category that owns this item.
     */
    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }

    /**
     * Get the order items for this menu item.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include available items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to only include featured items.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('menu_category_id', $categoryId);
    }

    /**
     * Scope to filter by dietary preferences.
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    public function scopeGlutenFree($query)
    {
        return $query->where('is_gluten_free', true);
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get the formatted preparation time.
     */
    public function getFormattedPreparationTimeAttribute()
    {
        if (!$this->preparation_time) return 'Time varies';
        
        if ($this->preparation_time < 60) {
            return $this->preparation_time . ' minutes';
        }
        
        $hours = floor($this->preparation_time / 60);
        $minutes = $this->preparation_time % 60;
        
        if ($minutes > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$hours}h";
    }

    /**
     * Get dietary badges for this item.
     */
    public function getDietaryBadgesAttribute()
    {
        $badges = [];

        if ($this->is_vegetarian) {
            $badges[] = [
                'label' => 'Vegetarian',
                'color' => 'green',
                'icon' => '🥬',
                'class' => 'bg-green-100 text-green-800'
            ];
        }
        
        if ($this->is_vegan) {
            $badges[] = [
                'label' => 'Vegan',
                'color' => 'emerald',
                'icon' => '🌱',
                'class' => 'bg-emerald-100 text-emerald-800'
            ];
        }
        
        if ($this->is_gluten_free) {
            $badges[] = [
                'label' => 'Gluten-Free',
                'color' => 'blue',
                'icon' => '🌾',
                'class' => 'bg-blue-100 text-blue-800'
            ];
        }
        
        if ($this->is_spicy) {
            $badges[] = [
                'label' => 'Spicy',
                'color' => 'red',
                'icon' => '🌶️',
                'class' => 'bg-red-100 text-red-800'
            ];
        }

        return $badges;
    }    /**
     * Get popularity score based on order count.
     */
    public function getPopularityScoreAttribute()
    {
        return $this->orderItems()->count();
    }

    /**
     * Check if item has allergens.
     */
    public function hasAllergens()
    {
        return !empty($this->allergens);
    }

    /**
     * Get allergens as array.
     */
    public function getAllergensArrayAttribute()
    {
        if (empty($this->allergens)) return [];
        
        return array_map('trim', explode(',', $this->allergens));
    }

    /**
     * Get ingredients as array.
     */
    public function getIngredientsArrayAttribute()
    {
        if (empty($this->ingredients)) return [];
        
        return array_map('trim', explode(',', $this->ingredients));
    }
}
