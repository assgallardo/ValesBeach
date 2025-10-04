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

    /**
     * Get the payments for the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the invoice for the booking.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
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

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '₱' . number_format((float) $this->total_price, 2);
    }

    /**
     * Check if booking is fully paid
     */
    public function isPaid()
    {
        return $this->payments()->where('status', 'completed')->sum('amount') >= $this->total_price;
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        $paid = $this->payments()->where('status', 'completed')->sum('amount');
        return max(0, $this->total_price - $paid);
    }

    /**
     * Get formatted remaining balance
     */
    public function getFormattedRemainingBalanceAttribute()
    {
        return '₱' . number_format((float) $this->remaining_balance, 2);
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * Get formatted total paid
     */
    public function getFormattedTotalPaidAttribute()
    {
        return '₱' . number_format((float) $this->total_paid, 2);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->isPaid()) {
            return 'paid';
        } elseif ($this->total_paid > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
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
