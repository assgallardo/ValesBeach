<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCleaningSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'booking_id',
        'assigned_to',
        'completed_by',
        'type',
        'priority',
        'status',
        'scheduled_date',
        'started_at',
        'completed_at',
        'notes',
        'special_instructions',
        'bed_made',
        'bathroom_cleaned',
        'floor_vacuumed',
        'trash_removed',
        'towels_replaced',
        'amenities_restocked',
        'surfaces_dusted',
        'linens_changed',
        'custom_checklist',
        'supplies_used',
        'images',
        'duration_minutes',
        'quality_rating',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'bed_made' => 'boolean',
        'bathroom_cleaned' => 'boolean',
        'floor_vacuumed' => 'boolean',
        'trash_removed' => 'boolean',
        'towels_replaced' => 'boolean',
        'amenities_restocked' => 'boolean',
        'surfaces_dusted' => 'boolean',
        'linens_changed' => 'boolean',
        'custom_checklist' => 'array',
        'supplies_used' => 'array',
        'images' => 'array',
        'quality_rating' => 'decimal:2',
    ];

    /**
     * Get the room being cleaned
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the associated booking (if applicable)
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user assigned to clean
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who completed the cleaning
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope to get scheduled cleanings
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to get in-progress cleanings
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed cleanings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get today's cleanings
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    /**
     * Scope to get overdue cleanings
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('scheduled_date', '<', now());
    }

    /**
     * Scope to get high priority cleanings
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Get formatted status
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get formatted type
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'text-green-600 bg-green-100 border-green-300',
            'in_progress' => 'text-blue-600 bg-blue-100 border-blue-300',
            'scheduled' => 'text-yellow-600 bg-yellow-100 border-yellow-300',
            'skipped' => 'text-gray-600 bg-gray-100 border-gray-300',
            'cancelled' => 'text-red-600 bg-red-100 border-red-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'text-red-600 bg-red-100 border-red-300',
            'high' => 'text-orange-600 bg-orange-100 border-orange-300',
            'normal' => 'text-blue-600 bg-blue-100 border-blue-300',
            'low' => 'text-gray-600 bg-gray-100 border-gray-300',
            default => 'text-gray-600 bg-gray-100 border-gray-300',
        };
    }

    /**
     * Check if cleaning is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed' && 
               $this->scheduled_date->isPast();
    }

    /**
     * Get checklist completion percentage
     */
    public function getChecklistCompletionAttribute(): int
    {
        $checklistItems = [
            'bed_made',
            'bathroom_cleaned',
            'floor_vacuumed',
            'trash_removed',
            'towels_replaced',
            'amenities_restocked',
            'surfaces_dusted',
            'linens_changed',
        ];

        $completed = 0;
        foreach ($checklistItems as $item) {
            if ($this->$item) {
                $completed++;
            }
        }

        return (int) (($completed / count($checklistItems)) * 100);
    }

    /**
     * Mark cleaning as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark cleaning as completed
     */
    public function markAsCompleted(?int $durationMinutes = null, ?float $qualityRating = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
            'duration_minutes' => $durationMinutes ?? ($this->started_at ? $this->started_at->diffInMinutes(now()) : null),
            'quality_rating' => $qualityRating,
        ]);
    }

    /**
     * Check if all checklist items are completed
     */
    public function isChecklistComplete(): bool
    {
        return $this->bed_made &&
               $this->bathroom_cleaned &&
               $this->floor_vacuumed &&
               $this->trash_removed &&
               $this->towels_replaced &&
               $this->amenities_restocked &&
               $this->surfaces_dusted &&
               $this->linens_changed;
    }
}
