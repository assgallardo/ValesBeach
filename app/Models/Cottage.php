<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cottage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'bedrooms',
        'bathrooms',
        'price_per_day',
        'price_per_hour',
        'weekend_rate',
        'holiday_rate',
        'security_deposit',
        'min_hours',
        'max_hours',
        'amenities',
        'features',
        'location',
        'size_sqm',
        'status',
        'allow_day_use',
        'allow_overnight',
        'allow_pets',
        'allow_events',
        'advance_booking_days',
        'primary_image',
        'images',
        'last_maintenance',
        'next_maintenance',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'price_per_hour' => 'decimal:2',
        'weekend_rate' => 'decimal:2',
        'holiday_rate' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'amenities' => 'array',
        'features' => 'array',
        'images' => 'array',
        'allow_day_use' => 'boolean',
        'allow_overnight' => 'boolean',
        'allow_pets' => 'boolean',
        'allow_events' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'last_maintenance' => 'datetime',
        'next_maintenance' => 'datetime',
    ];

    /**
     * Get all bookings for this cottage
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(CottageBooking::class);
    }

    /**
     * Get all images for this cottage
     */
    public function cottageImages(): HasMany
    {
        return $this->hasMany(CottageImage::class)->orderBy('sort_order');
    }

    /**
     * Scope to get active cottages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get available cottages
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get featured cottages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if cottage is available for given dates
     */
    public function isAvailableFor($checkIn, $checkOut): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        return !$this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->exists();
    }

    /**
     * Get formatted price per day
     */
    public function getFormattedPricePerDayAttribute(): string
    {
        return '₱' . number_format($this->price_per_day, 2);
    }

    /**
     * Get formatted price per hour
     */
    public function getFormattedPricePerHourAttribute(): string
    {
        return '₱' . number_format($this->price_per_hour ?? 0, 2);
    }

    /**
     * Get formatted weekend rate
     */
    public function getFormattedWeekendRateAttribute(): string
    {
        return '₱' . number_format($this->weekend_rate ?? $this->price_per_day, 2);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'text-green-600 bg-green-100 border-green-300',
            'occupied' => 'text-blue-600 bg-blue-100 border-blue-300',
            'reserved' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'maintenance' => 'text-orange-600 bg-orange-100 border-orange-300',
            'unavailable' => 'text-red-600 bg-red-100 border-red-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        if ($this->primary_image) {
            return asset('storage/' . $this->primary_image);
        }

        $firstImage = $this->cottageImages()->where('is_primary', true)->first();
        if ($firstImage) {
            return asset('storage/' . $firstImage->image_path);
        }

        return null;
    }

    /**
     * Get capacity description
     */
    public function getCapacityDescriptionAttribute(): string
    {
        $parts = [];
        
        if ($this->capacity) {
            $parts[] = "{$this->capacity} guests";
        }
        
        if ($this->bedrooms) {
            $parts[] = "{$this->bedrooms} " . ($this->bedrooms == 1 ? 'bedroom' : 'bedrooms');
        }
        
        if ($this->bathrooms) {
            $parts[] = "{$this->bathrooms} " . ($this->bathrooms == 1 ? 'bathroom' : 'bathrooms');
        }

        return implode(' • ', $parts);
    }

    /**
     * Calculate price for booking
     */
    public function calculatePrice($checkIn, $checkOut, $bookingType = 'day_use', $hours = null): float
    {
        $checkInDate = is_string($checkIn) ? \Carbon\Carbon::parse($checkIn)->startOfDay() : $checkIn->copy()->startOfDay();
        $checkOutDate = is_string($checkOut) ? \Carbon\Carbon::parse($checkOut)->startOfDay() : $checkOut->copy()->startOfDay();

        if ($bookingType === 'hourly' && $hours) {
            return ($this->price_per_hour ?? $this->price ?? 0) * $hours;
        }

        $days = $checkInDate->diffInDays($checkOutDate);
        if ($days == 0) {
            $days = 1; // Same-day booking counts as 1 day
        }

        // Use price_per_day if available, otherwise fallback to price
        $pricePerDay = $this->price_per_day ?? $this->price ?? 0;
        $basePrice = $pricePerDay * $days;

        // Add weekend surcharge if applicable
        $weekendDays = 0;
        for ($date = $checkInDate->copy(); $date->lt($checkOutDate) || ($days == 1 && $date->equalTo($checkOutDate)); $date->addDay()) {
            if ($date->isWeekend()) {
                $weekendDays++;
            }
            if ($days == 1) break; // For same-day bookings, only check the single day
        }

        if ($weekendDays > 0 && $this->weekend_rate) {
            $weekendSurcharge = ($this->weekend_rate - $pricePerDay) * $weekendDays;
            $basePrice += $weekendSurcharge;
        }

        return $basePrice;
    }
}
