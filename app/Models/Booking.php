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
        'amount_paid',
        'remaining_balance',
        'payment_status',
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
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2'
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a booking is being deleted, ensure all related payments are deleted
        static::deleting(function ($booking) {
            // Delete all associated payments
            // Note: This is a safeguard - cascade delete at database level should handle this automatically
            $booking->payments()->delete();
            
            \Log::info('Booking deleted - payments also removed', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status
            ]);
        });
    }

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

    /**
     * Get the housekeeping requests for the booking.
     */
    public function housekeepingRequests()
    {
        return $this->hasMany(\App\Models\HousekeepingRequest::class);
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

    /**
     * Update payment tracking after payment
     */
    public function updatePaymentTracking()
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        $remainingBalance = max(0, $this->total_price - $totalPaid);
        
        // Determine payment status
        $paymentStatus = 'unpaid';
        if ($totalPaid >= $this->total_price) {
            $paymentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'partial';
        }

        // Debug logging
        \Log::info('Booking updatePaymentTracking', [
            'booking_id' => $this->id,
            'total_price' => $this->total_price,
            'total_paid_calculated' => $totalPaid,
            'remaining_balance_calculated' => $remainingBalance,
            'payment_status' => $paymentStatus,
            'payment_count' => $this->payments()->where('status', 'completed')->count()
        ]);

        $this->update([
            'amount_paid' => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'payment_status' => $paymentStatus
        ]);
    }

    /**
     * Get minimum payment amount (50% of total)
     */
    public function getMinimumPaymentAttribute()
    {
        return $this->remaining_balance > 0 
            ? max(($this->total_price * 0.5) - $this->amount_paid, 0)
            : 0;
    }

    /**
     * Get formatted minimum payment
     */
    public function getFormattedMinimumPaymentAttribute()
    {
        return '₱' . number_format((float) $this->minimum_payment, 2);
    }

    /**
     * Check if booking accepts partial payment
     */
    public function canMakePartialPayment()
    {
        return $this->remaining_balance > 0 && $this->amount_paid < $this->total_price;
    }

    /**
     * Get payment status color for UI
     */
    public function getPaymentStatusColorAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'text-green-600 bg-green-100 border-green-300',
            'partial' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'unpaid' => 'text-red-600 bg-red-100 border-red-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300'
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'Fully Paid',
            'partial' => 'Partially Paid',
            'unpaid' => 'Unpaid',
            default => 'Unknown'
        };
    }
}