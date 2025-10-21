<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'reported_by',
        'assigned_to',
        'type',
        'priority',
        'status',
        'title',
        'description',
        'notes',
        'resolution_notes',
        'estimated_cost',
        'actual_cost',
        'scheduled_date',
        'started_at',
        'completed_at',
        'due_date',
        'images',
        'checklist',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'due_date' => 'datetime',
        'images' => 'array',
        'checklist' => 'array',
    ];

    /**
     * Get the room that this maintenance log belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who reported the issue
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user assigned to handle this maintenance
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to get pending maintenance
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get in-progress maintenance
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed maintenance
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get urgent maintenance
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope to get high priority maintenance
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope to get overdue maintenance
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('due_date', '<', now());
    }

    /**
     * Get formatted status for display
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get formatted type for display
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Get formatted priority for display
     */
    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'text-red-600 bg-red-100 border-red-300',
            'high' => 'text-orange-600 bg-orange-100 border-orange-300',
            'medium' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'low' => 'text-blue-600 bg-blue-100 border-blue-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'text-green-600 bg-green-100 border-green-300',
            'in_progress' => 'text-blue-600 bg-blue-100 border-blue-300',
            'pending' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'on_hold' => 'text-gray-600 bg-gray-100 border-gray-300',
            'cancelled' => 'text-red-600 bg-red-100 border-red-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Check if maintenance is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->status !== 'completed' && 
               $this->due_date->isPast();
    }

    /**
     * Get formatted estimated cost
     */
    public function getFormattedEstimatedCostAttribute(): string
    {
        return '₱' . number_format($this->estimated_cost ?? 0, 2);
    }

    /**
     * Get formatted actual cost
     */
    public function getFormattedActualCostAttribute(): string
    {
        return '₱' . number_format($this->actual_cost ?? 0, 2);
    }

    /**
     * Mark maintenance as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark maintenance as completed
     */
    public function markAsCompleted(string $resolutionNotes = null, float $actualCost = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'resolution_notes' => $resolutionNotes,
            'actual_cost' => $actualCost,
        ]);
    }
}
