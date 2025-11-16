<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'service_request_id',
        'food_order_id',
        'user_id',
        'payment_reference',
        'payment_transaction_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'payment_details',
        'payment_date',
        'notes',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'refunded_by',
        'item_description',
        'item_type',
        'item_details'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'payment_details' => 'array',
        'item_details' => 'array',
        'payment_date' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    /**
     * Generate payment reference
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (!$payment->payment_reference) {
                $payment->payment_reference = 'PAY-' . strtoupper(uniqid());
            }
            
            // Save snapshot of payment details for historical record
            $payment->saveItemSnapshot();
        });
        
        static::updating(function ($payment) {
            // Update snapshot if item details are not already saved
            if (empty($payment->item_description)) {
                $payment->saveItemSnapshot();
            }
        });
    }
    
    /**
     * Save a snapshot of the payment item details
     * This ensures completed payments remain accurate even if related entities are deleted
     */
    public function saveItemSnapshot()
    {
        if ($this->foodOrder) {
            $itemCount = $this->foodOrder->orderItems->count();
            $totalQuantity = $this->foodOrder->orderItems->sum('quantity');
            $this->item_description = "Food Order #{$this->foodOrder->order_number} ({$itemCount} items, {$totalQuantity} total)";
            $this->item_type = 'food_order';
            $this->item_details = [
                'order_number' => $this->foodOrder->order_number,
                'item_count' => $itemCount,
                'total_quantity' => $totalQuantity,
                'items' => $this->foodOrder->orderItems->map(function($item) {
                    return [
                        'name' => $item->menuItem->name ?? 'Unknown Item',
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ];
                })->toArray()
            ];
        } elseif ($this->serviceRequest) {
            $serviceName = $this->serviceRequest->service->name ?? $this->serviceRequest->service_type ?? 'Service Request';
            $quantity = $this->serviceRequest->quantity ?? 1;
            $this->item_description = "{$serviceName} (Qty: {$quantity})";
            $this->item_type = 'service';
            $this->item_details = [
                'service_name' => $serviceName,
                'quantity' => $quantity,
                'service_type' => $this->serviceRequest->service_type ?? null,
                'scheduled_date' => $this->serviceRequest->scheduled_date ?? null
            ];
        } elseif ($this->booking) {
            $checkIn = \Carbon\Carbon::parse($this->booking->check_in_date ?? $this->booking->check_in);
            $checkOut = \Carbon\Carbon::parse($this->booking->check_out_date ?? $this->booking->check_out);
            $nights = $checkIn->diffInDays($checkOut) ?: 1;
            $roomName = $this->booking->room->name ?? 'Room';
            $this->item_description = "Room: {$roomName} ({$nights} nights)";
            $this->item_type = 'booking';
            $this->item_details = [
                'room_name' => $roomName,
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'nights' => $nights
            ];
        } else {
            // Extra charge or other payment type
            $this->item_description = $this->notes ?? 'Payment';
            $this->item_type = 'extra_charge';
            $this->item_details = [];
        }
    }

    /**
     * Get the booking associated with the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the service request associated with the payment.
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the food order associated with the payment.
     */
    public function foodOrder()
    {
        return $this->belongsTo(FoodOrder::class);
    }

    /**
     * Get the user who made the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->calculated_amount, 2);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'online' => 'Online Payment',
            default => ucfirst(str_replace('_', ' ', $this->payment_method))
        };
    }

    /**
     * Get payment type (booking, service, or food order)
     */
    public function getPaymentTypeAttribute()
    {
        if ($this->food_order_id) {
            return 'food_order';
        } elseif ($this->service_request_id) {
            return 'service';
        } elseif ($this->booking_id) {
            return 'booking';
        }
        return 'unknown';
    }

    /**
     * Get payment category display name
     */
    public function getPaymentCategoryAttribute()
    {
        if ($this->food_order_id) {
            return 'Food Order';
        } elseif ($this->booking_id) {
            return 'Room Booking';
        } elseif ($this->service_request_id) {
            return 'Service Request';
        } else {
            return 'Other Payment';
        }
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded()
    {
        return $this->status === 'refunded' || $this->refund_amount > 0;
    }

    /**
     * Check if payment is partially refunded
     */
    public function isPartiallyRefunded()
    {
        return $this->refund_amount > 0 && $this->refund_amount < $this->amount;
    }

    /**
     * Calculate net amount after refunds
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - ($this->refund_amount ?? 0);
    }

    /**
     * Check if payment can be refunded
     */
    public function canBeRefunded()
    {
        return in_array($this->status, ['completed', 'confirmed']) && 
               ($this->refund_amount === null || $this->refund_amount < $this->calculated_amount);
    }

    /**
     * Get remaining refundable amount
     */
    public function getRemainingRefundableAmount()
    {
        return $this->calculated_amount - ($this->refund_amount ?? 0);
    }

    /**
     * Get refundable amount attribute (accessor for views)
     */
    public function getRefundableAmountAttribute()
    {
        return $this->getRemainingRefundableAmount();
    }

    /**
     * Get formatted refund amount
     */
    public function getFormattedRefundAmountAttribute()
    {
        return $this->refund_amount ? '₱' . number_format($this->refund_amount, 2) : null;
    }

    /**
     * Get the admin who processed the refund
     */
    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    /**
     * Scope for refunded payments
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope for refundable payments
     */
    public function scopeRefundable($query)
    {
        return $query->where('status', 'completed')
            ->where(function($q) {
                $q->where('refund_amount', '<', DB::raw('amount'))
                  ->orWhereNull('refund_amount');
            });
    }

    /**
     * Get detailed description for the payment
     */
    public function getDetailedDescriptionAttribute()
    {
        // If related entity was deleted, use saved snapshot
        if (!$this->foodOrder && !$this->booking && !$this->serviceRequest && $this->item_description) {
            return $this->item_description;
        }
        
        if ($this->foodOrder) {
            $itemCount = $this->foodOrder->orderItems->count();
            $totalQuantity = $this->foodOrder->orderItems->sum('quantity');
            
            return "Food Order #{$this->foodOrder->order_number} ({$itemCount} items, {$totalQuantity} total)";
        } elseif ($this->booking) {
            $checkIn = \Carbon\Carbon::parse($this->booking->check_in_date);
            $checkOut = \Carbon\Carbon::parse($this->booking->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            
            return "Room: {$this->booking->room->name} ({$nights} nights)";
        } elseif ($this->serviceRequest && $this->serviceRequest->service) {
            $service = $this->serviceRequest->service;
            $quantity = $this->serviceRequest->quantity ?? 1;
            
            return "{$service->name} (Qty: {$quantity})";
        }
        
        // Fallback to snapshot if available
        return $this->item_description ?? 'Payment';
    }

    /**
     * Get the calculated amount based on service/booking/food order
     * Falls back to the saved amount if related entities were deleted
     */
    public function getCalculatedAmountAttribute()
    {
        // If related entity was deleted, return the saved amount
        if (!$this->foodOrder && !$this->booking && !$this->serviceRequest) {
            return $this->amount;
        }
        
        if ($this->foodOrder) {
            return $this->foodOrder->total_amount;
        } elseif ($this->serviceRequest && $this->serviceRequest->service) {
            $service = $this->serviceRequest->service;
            $quantity = $this->serviceRequest->quantity ?? 1;
            return $service->price * $quantity;
        } elseif ($this->booking && $this->booking->room) {
            // Calculate booking amount
            $amount = 0;
            
            // Use the booking's date accessors which handle different column names
            $checkIn = $this->booking->check_in_date ?? $this->booking->check_in;
            $checkOut = $this->booking->check_out_date ?? $this->booking->check_out;
            
            if ($checkIn && $checkOut) {
                $checkIn = \Carbon\Carbon::parse($checkIn)->startOfDay();
                $checkOut = \Carbon\Carbon::parse($checkOut)->startOfDay();
                $nights = $checkIn->diffInDays($checkOut);
                
                // Same-day bookings count as 1 night/day
                if ($nights == 0) {
                    $nights = 1;
                }
                
                $amount += $this->booking->room->price * $nights;
            }
            
            $amount += $this->booking->additional_fees ?? 0;
            $amount -= $this->booking->discount_amount ?? 0;
            return max(0, $amount);
        }
        
        return $this->amount;
    }
}
