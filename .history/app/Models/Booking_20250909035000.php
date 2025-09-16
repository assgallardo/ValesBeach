<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'total_price',
        'status',
        'special_requests',
    ];

    /**
     * Get the user that made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that was booked.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the user that made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room that was booked.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '₱' . number_format((float)$this->total_price, 2);
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-500',
            'confirmed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            'completed' => 'bg-blue-500',
        ];

        $color = $colors[$this->status] ?? 'bg-gray-500';

        return '<span class="px-3 py-1 text-sm font-medium text-white rounded-full ' . $color . '">' 
            . ucfirst($this->status) 
            . '</span>';
    }
}
