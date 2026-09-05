<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'category_badge',
        'category_subtitle',
        'province',
        'address',
        'price_level',
        'rating',
        'reviews_count',
        'opening_hours',
        'description',
        'amenities',
        'gradient_thumb',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'amenities' => 'array',
        ];
    }

    /**
     * Get the tables, courts, or rooms for this venue.
     */
    public function items(): HasMany
    {
        return $this->hasMany(VenueItem::class)->orderBy('sort_order');
    }

    /**
     * Get the bookings for this venue.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
