<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
         'name',
        'key_number',
        'type',
        'description',
        'capacity',
        'beds',
        'price',
        'status',
        'is_available',
        'amenities',
        'check_in_time',
        'check_out_time',
        'category'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'amenities' => 'array'
    ];

    // Define default values
    protected $attributes = [
        'status' => 'available',
        'is_available' => true,
        'capacity' => 2,
        'price' => 0
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the images for the room.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->ordered();
    }

    /**
     * Get featured images for the room.
     */
    public function featuredImages(): HasMany
    {
        return $this->hasMany(RoomImage::class)->featured()->ordered();
    }

    /**
     * Get the main/featured image for the room
     */
    public function getMainImageAttribute(): ?string
    {
        $featuredImage = $this->featuredImages()->first();
        if ($featuredImage) {
            return $featuredImage->image_path;
        }
        
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->image_path : null;
    }

    /**
     * Format the price with PHP currency symbol
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get price as price_per_night for compatibility
     */
    public function getPricePerNightAttribute(): float
    {
        return (float)$this->price;
    }

    /**
     * Get status based on is_available
     */
    public function getStatusAttribute(): string
    {
        return $this->is_available ? 'available' : 'unavailable';
    }

    /**
     * Get size attribute (default since not in your database)
     */
    public function getSizeAttribute(): int
    {
        return 25; // default room size
    }

    /**
     * Check if the room is available for the given dates
     */
    public function isAvailableForDates($checkIn, $checkOut): bool
    {
        if (!$this->is_available) {
            return false;
        }

        return !$this->bookings()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<=', $checkOut)
                      ->where('check_out', '>=', $checkIn);
                });
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'available' => 'text-green-400',
            'occupied' => 'text-red-400',
            'maintenance' => 'text-yellow-400',
            'cleaning' => 'text-blue-400',
            default => 'text-gray-400'
        };
    }
}
