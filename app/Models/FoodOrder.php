<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FoodOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'booking_id',
        'order_number',
        'guest_name',
        'guest_email',
        'room_number',
        'phone_number',
        'status',
        'payment_status',
        'delivery_type',
        'delivery_location',
        'delivery_address',
        'special_instructions',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'total_amount',
        'requested_delivery_time',
        'estimated_delivery_time',
        'actual_delivery_time',
        'confirmed_at',
        'prepared_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'staff_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'requested_delivery_time' => 'datetime',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['orderItems'];

    /**
     * Get the user that owns this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the booking associated with this order.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the order items for this order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for this food order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include orders for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include preparing orders.
     */
    public function scopePreparing($query)
    {
        return $query->where('status', 'preparing');
    }

    /**
     * Scope a query to only include ready orders.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to filter by delivery type.
     */
    public function scopeByDeliveryType($query, $type)
    {
        return $query->where('delivery_type', $type);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Confirmation',
            'confirmed' => 'Order Confirmed',
            'preparing' => 'Being Prepared',
            'ready' => 'Ready for Delivery',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'preparing' => 'purple',
            'ready' => 'indigo',
            'out_for_delivery' => 'orange',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the payment status label.
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'Payment Pending',
            'paid' => 'Paid',
            'failed' => 'Payment Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->payment_status ?? 'pending')
        };
    }

    /**
     * Get the delivery type label.
     */
    public function getDeliveryTypeLabelAttribute()
    {
        return match($this->delivery_type) {
            'room_delivery' => 'Room Delivery',
            'restaurant_pickup' => 'Restaurant Pickup',
            'poolside_delivery' => 'Poolside Delivery',
            'beach_delivery' => 'Beach Delivery',
            default => ucfirst(str_replace('_', ' ', $this->delivery_type ?? 'room_delivery'))
        };
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '₱' . number_format((float)$this->total_amount, 2);
    }

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotalAttribute()
    {
        return '₱' . number_format((float)$this->subtotal, 2);
    }

    /**
     * Get the formatted tax amount.
     */
    public function getFormattedTaxAmountAttribute()
    {
        return '₱' . number_format((float)$this->tax_amount, 2);
    }

    /**
     * Get the formatted delivery fee.
     */
    public function getFormattedDeliveryFeeAttribute()
    {
        return '₱' . number_format((float)$this->delivery_fee, 2);
    }

    /**
     * Get the formatted estimated delivery time.
     */
    public function getFormattedEstimatedDeliveryTimeAttribute()
    {
        return $this->estimated_delivery_time 
            ? $this->estimated_delivery_time->format('M d, Y g:i A')
            : 'Time to be confirmed';
    }

    /**
     * Get the formatted actual delivery time.
     */
    public function getFormattedActualDeliveryTimeAttribute()
    {
        return $this->actual_delivery_time 
            ? $this->actual_delivery_time->format('M d, Y g:i A')
            : null;
    }

    /**
     * Get the total item count.
     */
    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               !$this->cancelled_at;
    }

    /**
     * Check if order is active.
     */
    public function isActive()
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted()
    {
        return in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber()
    {
        $prefix = 'VB';
        $date = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $random;
    }

    /**
     * Calculate totals for the order.
     */
    public function calculateTotals()
    {
        $subtotal = $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $taxRate = 0.12; // 12% VAT
        $taxAmount = $subtotal * $taxRate;
        
        $deliveryFee = $this->calculateDeliveryFee();
        
        $totalAmount = $subtotal + $taxAmount + $deliveryFee;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Calculate delivery fee based on delivery type.
     */
    private function calculateDeliveryFee()
    {
        return match($this->delivery_type) {
            'room_delivery' => 25.00,
            'poolside_delivery' => 15.00,
            'beach_delivery' => 35.00,
            'restaurant_pickup' => 0.00,
            default => 25.00
        };
    }

    /**
     * Get estimated preparation time based on order items.
     */
    public function getEstimatedPreparationTime()
    {
        $maxPrepTime = $this->orderItems->max(function ($item) {
            return $item->menuItem->preparation_time ?? 20;
        });

        // Add buffer time based on total items
        $bufferTime = min($this->total_items * 2, 15);
        
        return ($maxPrepTime ?? 20) + $bufferTime;
    }
}
