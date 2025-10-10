<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'assigned_by',
        'service_request_id',
        'status',
        'due_date',
        'completed_at',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user this task is assigned to
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who assigned this task
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the service request this task is related to
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status !== 'completed';
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-red-600 text-red-100';
            case 'confirmed':
                return 'bg-blue-600 text-blue-100';
            case 'assigned':
                return 'bg-yellow-600 text-yellow-100';
            case 'in_progress':
                return 'bg-orange-600 text-orange-100';
            case 'completed':
                return 'bg-green-600 text-green-100';
            case 'cancelled':
                return 'bg-gray-600 text-gray-100';
            default:
                return 'bg-gray-600 text-gray-100';
        }
    }

    /**
     * Scope: Get tasks for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope: Get active tasks (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * Scope: Get overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }
}
