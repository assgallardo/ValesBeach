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
        'reservation_id',
        'guest_name',
        'guest_email',
        'room_number',
        'description',
        'status',
        'priority',
        'requested_at',
        'scheduled_at',
        'completed_at',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('requested_at', today());
    }

    // Accessors
    public function getFormattedRequestedAtAttribute()
    {
        return $this->requested_at->format('M d, Y g:i A');
    }
}
