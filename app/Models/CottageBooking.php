<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CottageBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'cottage_id',
        'user_id',
        'booking_type',
        'check_in_date',
        'check_out_date',
        'check_in_time',
        'check_out_time',
        'hours',
        'nights',
        'guests',
        'children',
        'special_requests',
        'base_price',
        'additional_guest_fee',
        'extra_hours_fee',
        'weekend_surcharge',
        'holiday_surcharge',
        'security_deposit',
        'total_price',
        'amount_paid',
        'remaining_balance',
        'payment_status',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'addons',
        'admin_notes',
        'guest_notes',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'base_price' => 'decimal:2',
        'additional_guest_fee' => 'decimal:2',
        'extra_hours_fee' => 'decimal:2',
        'weekend_surcharge' => 'decimal:2',
        'holiday_surcharge' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'addons' => 'array',
    ];

    /**
     * Get the cottage for this booking
     */
    public function cottage(): BelongsTo
    {
        return $this->belongsTo(Cottage::class);
    }

    /**
     * Get the user who made the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all payments for this booking
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cottage_booking_id');
    }

    /**
     * Boot method to generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_reference) {
                $booking->booking_reference = 'COT-' . strtoupper(uniqid());
            }

            // Set initial remaining balance
            if (!isset($booking->remaining_balance)) {
                $booking->remaining_balance = $booking->total_price;
            }
        });
    }

    /**
     * Scope for active bookings
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', now())
                    ->whereIn('status', ['confirmed', 'pending']);
    }

    /**
     * Scope for current bookings
     */
    public function scopeCurrent($query)
    {
        return $query->where('check_in_date', '<=', now())
                    ->where('check_out_date', '>=', now())
                    ->where('status', 'checked_in');
    }

    /**
     * Update payment tracking after a payment
     */
    public function updatePaymentTracking(): void
    {
        $totalPaid = $this->payments()
            ->where('status', 'completed')
            ->sum('amount');

        $this->amount_paid = $totalPaid;
        $this->remaining_balance = max(0, $this->total_price - $totalPaid);

        // Update payment status
        if ($this->remaining_balance == 0) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }

    /**
     * Get minimum payment (50% of remaining balance or total)
     */
    public function getMinimumPaymentAttribute(): float
    {
        $remaining = $this->remaining_balance > 0 ? $this->remaining_balance : $this->total_price;
        return max(($remaining * 0.5), 1);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        $price = $this->total_price;
        
        // Recalculate if price is 0 and booking exists
        if ($price == 0 && $this->cottage && $this->check_in_date && $this->check_out_date) {
            $price = $this->cottage->calculatePrice(
                $this->check_in_date,
                $this->check_out_date,
                $this->booking_type ?? 'day_use',
                $this->hours ?? null
            );
        }
        
        return '₱' . number_format($price, 2);
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return '₱' . number_format($this->amount_paid, 2);
    }

    /**
     * Get formatted remaining balance
     */
    public function getFormattedRemainingBalanceAttribute(): string
    {
        return '₱' . number_format($this->remaining_balance, 2);
    }

    /**
     * Get payment status color
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'text-green-600 bg-green-100 border-green-300',
            'partial' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'unpaid' => 'text-red-600 bg-red-100 border-red-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmed' => 'text-green-600 bg-green-100 border-green-300',
            'checked_in' => 'text-blue-600 bg-blue-100 border-blue-300',
            'checked_out' => 'text-purple-600 bg-purple-100 border-purple-300',
            'completed' => 'text-green-600 bg-green-100 border-green-300',
            'pending' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'cancelled' => 'text-red-600 bg-red-100 border-red-300',
            'no_show' => 'text-gray-600 bg-gray-100 border-gray-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'Fully Paid',
            'partial' => 'Partially Paid',
            'unpaid' => 'Unpaid',
            default => 'Unknown',
        };
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
               $this->check_in_date->isFuture();
    }

    /**
     * Cancel the booking
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Confirm the booking
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Check in
     */
    public function checkIn(): void
    {
        $this->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Check out
     */
    public function checkOut(): void
    {
        $this->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);
    }
}
