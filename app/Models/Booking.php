<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

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
        'payment_status',
        'status',
        'special_requests',
        'booking_reference',
        'early_checkin',
        'early_checkin_time',
        'early_checkin_fee',
        'late_checkout',
        'late_checkout_time',
        'late_checkout_fee'
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
        'early_checkin' => 'boolean',
        'late_checkout' => 'boolean',
        'early_checkin_fee' => 'decimal:2',
        'late_checkout_fee' => 'decimal:2'
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
        $price = $this->attributes['total_price'] ?? 
               $this->attributes['total_amount'] ?? 
               $this->attributes['price'] ?? 
               $this->attributes['amount'] ?? 0;
        
        // Convert to float for comparison
        $price = (float) $price;
        
        // Recalculate if price is 0 for same-day bookings
        if ($price == 0 && isset($this->attributes['room_id'])) {
            // Load room relationship if not already loaded
            if (!$this->relationLoaded('room')) {
                $this->load('room');
            }
            
            // Get check-in and check-out dates from attributes
            $checkInValue = $this->attributes['check_in'] ?? null;
            $checkOutValue = $this->attributes['check_out'] ?? null;
            
            if ($this->room && $checkInValue && $checkOutValue) {
                try {
                    $checkIn = \Carbon\Carbon::parse($checkInValue)->startOfDay();
                    $checkOut = \Carbon\Carbon::parse($checkOutValue)->startOfDay();
                    $nights = $checkIn->diffInDays($checkOut);
                    
                    // Same-day booking counts as 1 night
                    // Note: diffInDays returns float, use == not ===
                    if ($nights == 0) {
                        $nights = 1;
                    }
                    
                    $price = ($this->room->price ?? 0) * $nights;
                } catch (\Exception $e) {
                    // If date parsing fails, keep price as is
                }
            }
        }
        
        return $price;
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
        $price = (float) ($this->attributes['total_price'] ?? 0);
        
        // Recalculate if price is 0 for same-day bookings
        if ($price == 0 && isset($this->attributes['room_id'])) {
            // Load room relationship if not already loaded
            if (!$this->relationLoaded('room')) {
                $this->load('room');
            }
            
            if ($this->room) {
                try {
                    // Try to get dates - they might be already cast to Carbon or still strings
                    $checkIn = isset($this->attributes['check_in']) 
                        ? (\Carbon\Carbon::parse($this->attributes['check_in'])->startOfDay())
                        : null;
                    $checkOut = isset($this->attributes['check_out']) 
                        ? (\Carbon\Carbon::parse($this->attributes['check_out'])->startOfDay())
                        : null;
                    
                    if ($checkIn && $checkOut) {
                        $nights = $checkIn->diffInDays($checkOut);
                        
                        // Same-day booking counts as 1 night
                        // Note: diffInDays returns float, use == not ===
                        if ($nights == 0) {
                            $nights = 1;
                        }
                        
                        $price = ($this->room->price ?? 0) * $nights;
                    }
                } catch (\Exception $e) {
                    // If date parsing fails, keep price as 0
                    \Log::error('Failed to calculate booking price', [
                        'booking_id' => $this->id ?? 'unknown',
                        'check_in' => $this->attributes['check_in'] ?? 'not set',
                        'check_out' => $this->attributes['check_out'] ?? 'not set',
                        'room_price' => $this->room->price ?? 'no room',
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return '₱' . number_format($price, 2);
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
     * Update payment status after payment
     */
    public function updatePaymentTracking()
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        
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
            'remaining_balance_calculated' => max(0, $this->total_price - $totalPaid),
            'payment_status' => $paymentStatus,
            'payment_count' => $this->payments()->where('status', 'completed')->count()
        ]);

        // Only update payment_status (amount_paid and remaining_balance are now calculated dynamically)
        $this->update([
            'payment_status' => $paymentStatus
        ]);
    }

    /**
     * Get minimum payment amount (50% of total)
     */
    public function getMinimumPaymentAttribute()
    {
        return $this->remaining_balance > 0 
            ? max(($this->total_price * 0.5) - $this->total_paid, 0)
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
        return $this->remaining_balance > 0 && $this->total_paid < $this->total_price;
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

    /**
     * Get total with early check-in and late checkout fees
     */
    public function getGrandTotalAttribute()
    {
        $total = (float) $this->total_price;
        
        if ($this->early_checkin) {
            $total += (float) $this->early_checkin_fee;
        }
        
        if ($this->late_checkout) {
            $total += (float) $this->late_checkout_fee;
        }
        
        return $total;
    }

    /**
     * Get formatted grand total
     */
    public function getFormattedGrandTotalAttribute()
    {
        return '₱' . number_format($this->grand_total, 2);
    }

    /**
     * Check if booking has early check-in or late checkout
     */
    public function hasSpecialTiming()
    {
        return $this->early_checkin || $this->late_checkout;
    }
}