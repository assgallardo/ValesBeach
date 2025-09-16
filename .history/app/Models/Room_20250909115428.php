<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'type',
        'description',
        'price',
        'capacity',
        'amenities',
        'images',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amenities' => 'array',
        'images' => 'array',
        'is_available' => 'boolean'
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Format the price with PHP currency symbol
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱ ' . number_format((float)$this->price, 2);
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
}
