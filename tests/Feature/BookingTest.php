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

    public function test_user_can_create_booking_with_deposit_amount(): void
    {
        $venue = Venue::where('deposit_amount', '>', 0)->first();
        $item = $venue->items->first();

        $response = $this->postJson('/bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '19:00',
            'party_size' => 2,
            'customer_name' => 'สิทธิรัช พรหมคุณ',
            'customer_phone' => '089-999-8888',
            'deposit_amount' => 250.00,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'booking' => [
                'deposit_amount' => '250.00',
                'status' => 'confirmed',
            ],
        ]);

        $this->assertDatabaseHas('bookings', [
            'venue_id' => $venue->id,
            'customer_name' => 'สิทธิรัช พรหมคุณ',
            'deposit_amount' => 250.00,
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

    public function test_user_can_create_booking_with_time_range(): void
    {
        $venue = Venue::first();
        // Use an item that has no conflicting booking
        $item = $venue->items->last();

        $response = $this->postJson('/bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'booking_date' => now()->addDays(2)->toDateString(),
            'booking_time' => '17:00',
            'booking_end_time' => '19:00',
            'party_size' => 2,
            'customer_name' => 'สมเกียรติ ปลอดโปร่ง',
            'customer_phone' => '086-111-2222',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'booking' => [
                'booking_time' => '17:00',
                'booking_end_time' => '19:00',
            ],
        ]);
    }

    public function test_system_prevents_overlapping_bookings_on_same_item(): void
    {
        $venue = Venue::first();
        $item = $venue->items->last();
        $date = now()->addDays(5)->toDateString();

        // 1st booking: 18:00 - 20:00
        Booking::create([
            'booking_code' => 'RSV-TEST-0001',
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'customer_name' => 'คนแรก',
            'booking_date' => $date,
            'booking_time' => '18:00',
            'booking_end_time' => '20:00',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);

        // 2nd booking attempt: 19:00 - 21:00 (overlaps with 18:00-20:00)
        $response = $this->postJson('/bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'booking_date' => $date,
            'booking_time' => '19:00',
            'booking_end_time' => '21:00',
            'party_size' => 2,
            'customer_name' => 'คนที่สอง (ซ้อน)',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_system_allows_booking_same_item_after_previous_booking_ends(): void
    {
        $venue = Venue::first();
        $item = $venue->items->last();
        $date = now()->addDays(6)->toDateString();

        // 1st booking: 17:00 - 19:00
        Booking::create([
            'booking_code' => 'RSV-TEST-0002',
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'customer_name' => 'รอบหัวค่ำ',
            'booking_date' => $date,
            'booking_time' => '17:00',
            'booking_end_time' => '19:00',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);

        // 2nd booking: 19:00 - 21:00 (starts when previous ends -> NOT overlapping)
        $response = $this->postJson('/bookings', [
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'booking_date' => $date,
            'booking_time' => '19:00',
            'booking_end_time' => '21:00',
            'party_size' => 2,
            'customer_name' => 'รอบดึก (ไม่ชน)',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_availability_endpoint_returns_booked_items_and_schedules(): void
    {
        $venue = Venue::first();
        $item = $venue->items->last();
        $date = now()->addDays(7)->toDateString();

        Booking::create([
            'booking_code' => 'RSV-TEST-0003',
            'venue_id' => $venue->id,
            'venue_item_id' => $item->id,
            'customer_name' => 'ทดสอบตาราง',
            'booking_date' => $date,
            'booking_time' => '18:00',
            'booking_end_time' => '20:00',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);

        // Query during overlapping time 18:30 - 20:30 -> item should be in booked_item_ids
        $resOverlapping = $this->getJson("/venues/{$venue->id}/availability?date={$date}&start_time=18:30&end_time=20:30");
        $resOverlapping->assertStatus(200);
        $resOverlapping->assertJson([
            'success' => true,
        ]);
        $this->assertContains($item->id, $resOverlapping->json('booked_item_ids'));
        $this->assertArrayHasKey($item->id, $resOverlapping->json('items_schedule'));

        // Query during non-overlapping time 20:00 - 22:00 -> item should NOT be in booked_item_ids (it is free!)
        $resFree = $this->getJson("/venues/{$venue->id}/availability?date={$date}&start_time=20:00&end_time=22:00");
        $resFree->assertStatus(200);
        $this->assertNotContains($item->id, $resFree->json('booked_item_ids'));
    }
}
