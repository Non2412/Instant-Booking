<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'venue_id',
        'venue_item_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'booking_date',
        'booking_time',
        'booking_end_time',
        'party_size',
        'status',
        'deposit_amount',
        'special_request',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'party_size' => 'integer',
            'deposit_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the venue that was booked.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the table or court that was booked.
     */
    public function venueItem(): BelongsTo
    {
        return $this->belongsTo(VenueItem::class, 'venue_item_id');
    }

    /**
     * Get the end time for this booking (defaults to start time + 2 hours).
     */
    public function getEndTime(): string
    {
        if ($this->booking_end_time) {
            return $this->booking_end_time;
        }

        try {
            $parts = explode(':', $this->booking_time);
            $h = (int) $parts[0] + 2;
            $m = $parts[1] ?? '00';

            return sprintf('%02d:%s', min($h, 23), $m);
        } catch (\Throwable) {
            return '21:00';
        }
    }

    /**
     * Get human-readable time range string, e.g. "18:00 – 20:00 น."
     */
    public function getTimeRangeLabel(): string
    {
        return "{$this->booking_time} – {$this->getEndTime()} น.";
    }
}
