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
}
