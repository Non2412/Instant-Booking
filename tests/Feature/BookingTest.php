<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Venue;
use Database\Seeders\VenueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VenueSeeder::class);
    }

    public function test_homepage_loads_successfully_with_venues(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('จองทันใจ');
        $response->assertSee('ครัวคุณยาย');
        $response->assertSee('อารีน่า สปอร์ตคลับ');
        $response->assertSee('กรีนลีฟ คาเฟ่');
    }

    public function test_user_can_create_a_booking(): void
    {
        $venue = Venue::first();
        $item = $venue->items->first();

        $response = $this->postJson('/bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '19:00',
            'party_size' => 4,
            'customer_name' => 'สมชาย ขยันงาน',
            'customer_phone' => '081-234-5678',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'จองสำเร็จแล้ว',
        ]);

        $this->assertDatabaseHas('bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'customer_name' => 'สมชาย ขยันงาน',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);
    }

    public function test_booking_validation_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/bookings', [
            'venue_id' => 99999,
            'venue_item_id' => 99999,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['venue_id', 'venue_item_id', 'booking_date', 'booking_time', 'party_size', 'customer_name']);
    }

    public function test_owner_can_update_booking_status(): void
    {
        $booking = Booking::first();

        $response = $this->patchJson("/bookings/{$booking->id}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => 'confirmed',
            ],
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }
}
