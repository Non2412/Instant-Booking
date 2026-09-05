<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display the main application interface with venues, user bookings, and owner dashboard data.
     */
    public function index(Request $request): View
    {
        $venues = Venue::with('items')->get();

        // Default venue for detail & owner dashboard
        $selectedVenue = $venues->firstWhere('name', 'ครัวคุณยาย') ?? $venues->first() ?? new Venue([
            'name' => 'ครัวคุณยาย',
            'type' => 'restaurant',
            'category_badge' => 'ร้านอาหาร',
            'category_subtitle' => 'อาหารไทย-อีสาน · ชัยภูมิ · ฿฿',
            'address' => 'อ.เมือง, ชัยภูมิ',
            'opening_hours' => 'เปิด 11:00–21:30',
            'description' => 'ร้านอาหารไทย-อีสานบรรยากาศบ้านสวน',
            'rating' => 4.7,
            'reviews_count' => 312,
            'amenities' => ['ที่จอดรถฟรี', 'Wi-Fi'],
        ]);

        // Bookings for user (สมชาย ขยันงาน)
        $userBookings = Booking::with(['venue', 'venueItem'])
            ->where('customer_name', 'สมชาย ขยันงาน')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();

        // Today's bookings for owner dashboard
        $today = Carbon::today()->toDateString();
        $ownerBookings = Booking::with(['venue', 'venueItem'])
            ->where('venue_id', $selectedVenue?->id)
            ->whereDate('booking_date', $today)
            ->orderBy('booking_time', 'asc')
            ->get();

        // Owner stats
        $totalTodayBookings = $ownerBookings->count();
        $totalItemsCount = $selectedVenue ? $selectedVenue->items->count() : 12;
        $takenItemsCount = $ownerBookings->whereIn('status', ['confirmed', 'pending_deposit'])->pluck('venue_item_id')->unique()->filter()->count();
        $freeItemsCount = max(0, $totalItemsCount - $takenItemsCount);
        $pendingCount = $ownerBookings->where('status', 'pending_deposit')->count();
        $todayRevenue = $ownerBookings->where('status', 'confirmed')->count() * 400 + $ownerBookings->where('status', 'pending_deposit')->sum('deposit_amount');

        return view('bookings.index', [
            'venues' => $venues,
            'selectedVenue' => $selectedVenue,
            'userBookings' => $userBookings,
            'ownerBookings' => $ownerBookings,
            'todayDate' => $today,
            'stats' => [
                'today_bookings' => $totalTodayBookings,
                'free_items' => "{$freeItemsCount} / {$totalItemsCount}",
                'free_count' => $freeItemsCount,
                'total_count' => $totalItemsCount,
                'pending_count' => $pendingCount,
                'today_revenue' => '฿'.number_format($todayRevenue > 0 ? $todayRevenue : 9400),
            ],
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => ['required', 'exists:venues,id'],
            'venue_item_id' => ['required', 'exists:venue_items,id'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'string'],
            'party_size' => ['required', 'integer', 'min:1', 'max:50'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'special_request' => ['nullable', 'string', 'max:500'],
        ]);

        // Generate clean unique booking code (e.g. RSV-2609-XXXX)
        $datePart = Carbon::parse($validated['booking_date'])->format('ym');
        $randomPart = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $bookingCode = "RSV-{$datePart}-{$randomPart}";

        while (Booking::where('booking_code', $bookingCode)->exists()) {
            $randomPart = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $bookingCode = "RSV-{$datePart}-{$randomPart}";
        }

        $booking = Booking::create([
            'booking_code' => $bookingCode,
            'venue_id' => $validated['venue_id'],
            'venue_item_id' => $validated['venue_item_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? '081-234-5678',
            'customer_email' => $validated['customer_email'] ?? 'somchai@example.com',
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'party_size' => $validated['party_size'],
            'status' => 'confirmed',
            'deposit_amount' => 0,
            'special_request' => $validated['special_request'] ?? null,
        ]);

        $booking->load(['venue', 'venueItem']);

        return response()->json([
            'success' => true,
            'message' => 'จองสำเร็จแล้ว',
            'booking' => $booking,
        ], 201);
    }

    /**
     * Update the specified booking status (e.g. from owner dashboard).
     */
    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,pending_deposit,completed,cancelled'],
        ]);

        $booking->update(['status' => $validated['status']]);
        $booking->load(['venue', 'venueItem']);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะสำเร็จ',
            'booking' => $booking,
        ]);
    }

    /**
     * Get availability for a venue on a given date and time.
     */
    public function availability(Request $request, Venue $venue): JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $time = $request->query('time', '19:00');

        $bookedItemIds = Booking::where('venue_id', $venue->id)
            ->whereDate('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['confirmed', 'pending_deposit'])
            ->pluck('venue_item_id')
            ->toArray();

        return response()->json([
            'venue_id' => $venue->id,
            'date' => $date,
            'time' => $time,
            'booked_item_ids' => $bookedItemIds,
        ]);
    }
}
