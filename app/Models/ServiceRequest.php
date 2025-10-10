<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'reservation_id',
        'guest_name',
        'guest_email',
        'room_number',
        'requested_date',
        'requested_time',
        'guests',
        'special_requests',
        'description',
        'status',
        'priority',
        'requested_at',
        'scheduled_at',
        'completed_at',
        'confirmed_at',
        'assigned_to',
        'notes',
        'manager_notes'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'requested_time' => 'datetime:H:i',
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'confirmed_at' => 'datetime'
    ];

    /**
     * Get the user that made the service request (for guest bookings)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service being requested
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the reservation associated with this request
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the staff member assigned to this request
     */
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('requested_date', [$startDate, $endDate]);
    }

    /**
     * Scope for guest bookings (has user_id)
     */
    public function scopeGuestBookings($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope for service requests (no user_id)
     */
    public function scopeServiceRequests($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Get formatted date and time (for guest bookings)
     */
    public function getFormattedDateTimeAttribute()
    {
        if ($this->requested_date && $this->requested_time) {
            return $this->requested_date->format('M d, Y') . ' at ' . $this->requested_time->format('g:i A');
        }
        
        if ($this->requested_at) {
            return $this->requested_at->format('M d, Y g:i A');
        }
        
        return 'Not specified';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'assigned' => 'blue',
            'in_progress' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled()
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        // For guest bookings
        if ($this->requested_date) {
            return $this->requested_date->gt(Carbon::today());
        }

        // For service requests
        if ($this->scheduled_at) {
            return $this->scheduled_at->gt(Carbon::now());
        }

        return true;
    }

    /**
     * Get the display name for this request
     */
    public function getDisplayNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->guest_name;
    }

    /**
     * Check if this is a guest booking
     */
    public function isGuestBooking()
    {
        return !is_null($this->user_id);
    }

    /**
     * Auto-populate guest info when creating guest bookings
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($serviceRequest) {
            // Auto-populate guest info for guest bookings
            if ($serviceRequest->user_id && !$serviceRequest->guest_name) {
                $user = User::find($serviceRequest->user_id);
                if ($user) {
                    $serviceRequest->guest_name = $user->name;
                    $serviceRequest->guest_email = $user->email;
                }
            }

            // Set requested_at if not set
            if (!$serviceRequest->requested_at) {
                $serviceRequest->requested_at = now();
            }
        });
    }
}
