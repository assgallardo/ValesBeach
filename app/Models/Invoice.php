<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_number',
        'booking_id',
        'user_id',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'status',
        'invoice_date',
        'issue_date',
        'due_date',
        'paid_date',
        'notes',
        'line_items',
        'items',
        'created_by',
        'payment_transaction_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'invoice_date' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'line_items' => 'array',
        'items' => 'array'
    ];

    /**
     * Generate invoice number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
            
            // Calculate tax amount if not set
            if (!$invoice->tax_amount) {
                $invoice->tax_amount = ($invoice->subtotal * $invoice->tax_rate) / 100;
            }
            
            // Calculate total amount if not set
            if (!$invoice->total_amount) {
                $invoice->total_amount = $invoice->subtotal + $invoice->tax_amount;
            }
        });
    }

    /**
     * Generate unique invoice number for current year
     */
    protected static function generateInvoiceNumber()
    {
        $year = date('Y');
        $prefix = 'INV-' . $year . '-';
        
        // Get the latest invoice number for this year
        $latestInvoice = static::where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('invoice_number', 'DESC')
            ->lockForUpdate()
            ->first();
        
        if ($latestInvoice) {
            // Extract the number part and increment
            $lastNumber = (int) substr($latestInvoice->invoice_number, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            // First invoice of the year
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the booking associated with the invoice.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user associated with the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payments associated with the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    /**
     * Get formatted amounts
     */
    public function getFormattedSubtotalAttribute()
    {
        return '₱' . number_format((float) $this->subtotal, 2);
    }

    public function getFormattedTaxAmountAttribute()
    {
        return '₱' . number_format((float) $this->tax_amount, 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return '₱' . number_format((float) $this->total_amount, 2);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'overdue' || ($this->due_date < now() && $this->status !== 'paid');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now()
        ]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-500 text-white',
            'sent' => 'bg-blue-500 text-white',
            'paid' => 'bg-green-500 text-white',
            'overdue' => 'bg-red-500 text-white',
            'cancelled' => 'bg-gray-600 text-white',
            default => 'bg-gray-500 text-white'
        };
    }

    /**
     * Scope for overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['paid', 'cancelled']);
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
