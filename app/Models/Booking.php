<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'total_price',
        'status',
        'special_requests',
        'booking_reference'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that is booked.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Dynamic accessors with proper date handling
    /**
     * Get the check-in date.
     */
    public function getCheckInDateAttribute()
    {
        // Try different column names and ensure proper date formatting
        $dateValue = $this->attributes['check_in_date'] ?? 
                    $this->attributes['checkin_date'] ?? 
                    $this->attributes['check_in'] ?? 
                    $this->attributes['start_date'] ?? null;
        
        if ($dateValue && !$dateValue instanceof Carbon) {
            return Carbon::parse($dateValue);
        }
        
        return $dateValue;
    }

    /**
     * Get the check-out date.
     */
    public function getCheckOutDateAttribute()
    {
        $dateValue = $this->attributes['check_out_date'] ?? 
                    $this->attributes['checkout_date'] ?? 
                    $this->attributes['check_out'] ?? 
                    $this->attributes['end_date'] ?? null;
        
        if ($dateValue && !$dateValue instanceof Carbon) {
            return Carbon::parse($dateValue);
        }
        
        return $dateValue;
    }

    /**
     * Get the number of guests.
     */
    public function getGuestsAttribute()
    {
        return $this->attributes['guests'] ?? 
               $this->attributes['guest_count'] ?? 
               $this->attributes['number_of_guests'] ?? 1;
    }

    /**
     * Get the total price.
     */
    public function getTotalPriceAttribute()
    {
        return $this->attributes['total_price'] ?? 
               $this->attributes['total_amount'] ?? 
               $this->attributes['price'] ?? 
               $this->attributes['amount'] ?? 0;
    }

    /**
     * Get the booking reference.
     */
    public function getBookingReferenceAttribute()
    {
        return $this->attributes['booking_reference'] ?? 
               $this->attributes['reference'] ?? 
               $this->attributes['booking_id'] ?? 
               'VB' . $this->id;
    }

    // Helper method to format dates safely
    public static function formatDate($date, $format = 'M d, Y')
    {
        if (!$date) return 'N/A';
        
        try {
            if ($date instanceof Carbon) {
                return $date->format($format);
            }
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return 'Invalid Date';
        }
    }
}
