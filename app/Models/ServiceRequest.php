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
        'guest_id',
        'user_id',
        'guest_name',
        'guest_email',
        'room_id',
        'room_number',
        'service_type',
        'type',
        'service_name',
        'description',
        'scheduled_date',
        'scheduled_at',
        'booking_date',
        'appointment_date',
        'deadline',
        'guests_count',
        'guest_count',
        'number_of_guests',
        'pax',
        'manager_notes',
        'notes',
        'special_requests',
        'comments',
        'status',
        'priority',
        'assigned_to',
        'assigned_at',
        'estimated_duration',
        'completed_at',
        'cancelled_at',
        'requested_at'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'scheduled_date' => 'datetime',
        'estimated_duration' => 'integer',
        'guests_count' => 'integer'
    ];

    /**
     * Get deadline status
     */
    public function getDeadlineStatusAttribute()
    {
        if (!$this->deadline) {
            return 'no_deadline';
        }

        $now = now();
        $deadline = $this->deadline;
        
        if ($deadline->isPast()) {
            return 'overdue';
        } elseif ($deadline->diffInHours($now) <= 2) {
            return 'urgent';
        } elseif ($deadline->diffInHours($now) <= 6) {
            return 'soon';
        } else {
            return 'normal';
        }
    }

    /**
     * Get deadline status color
     */
    public function getDeadlineColorAttribute()
    {
        switch ($this->deadline_status) {
            case 'overdue':
                return 'bg-red-600 text-red-100';
            case 'urgent':
                return 'bg-orange-600 text-orange-100';
            case 'soon':
                return 'bg-yellow-600 text-yellow-100';
            case 'normal':
                return 'bg-blue-600 text-blue-100';
            default:
                return 'bg-gray-600 text-gray-100';
        }
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute()
    {
        if (!$this->deadline) {
            return 'No deadline set';
        }

        return $this->deadline->format('M d, Y H:i');
    }

    /**
     * Relationship: Service request belongs to a service
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Relationship: Service request belongs to a user (guest)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Legacy relationship: Service request belongs to a guest
     */
    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    /**
     * Relationship: Service request belongs to a room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relationship: Service request is assigned to a staff member
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Alias for assignedTo relationship (for consistency with controllers)
     */
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship: Service request may have a related task
     */
    public function task()
    {
        return $this->hasOne(Task::class);
    }

    /**
     * Check if the service request can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->requested_date >= now()->toDateString();
    }

    /**
     * Scope: Get pending requests
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope: Get assigned requests
     */
    public function scopeAssigned($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress']);
    }

    /**
     * Scope: Get completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get overdue requests
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope: Exclude cancelled requests from main list
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
