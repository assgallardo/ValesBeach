<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
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
        'category',
        'price',
        'duration',
        'capacity',
        'image',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Scope a query to only include active services.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return 'Not specified';
        
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'spa' => 'Spa',
            'dining' => 'Dining',
            'activities' => 'Activities',
            'transportation' => 'Transportation',
            'room_service' => 'Room Service',
            default => ucfirst($this->category)
        };
    }

    /**
     * Get the count of today's service requests.
     */
    public function getTodayRequestsCountAttribute()
    {
        return 0; // Will be updated once service_requests table exists
    }
}
