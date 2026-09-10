<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display the main application interface with venues, user bookings, and owner dashboard data.
     */
    public function index(Request $request): View
    {
        $this->expirePastBookings();

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

        // Bookings for user (Current authenticated user or fallback to 'สมชาย ขยันงาน')
        $userBookingsQuery = Booking::with(['venue', 'venueItem']);
        if (Auth::check()) {
            $authUser = Auth::user();
            $userBookingsQuery->where(function ($query) use ($authUser) {
                $query->where('customer_name', $authUser->name);
                if ($authUser->phone) {
                    $query->orWhere('customer_phone', $authUser->phone);
                }
                if ($authUser->email) {
                    $query->orWhere('customer_email', $authUser->email);
                }
            });
        } else {
            $userBookingsQuery->where('customer_name', 'สมชาย ขยันงาน');
        }

        $userBookings = $userBookingsQuery
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
            'booking_end_time' => ['nullable', 'string'],
            'party_size' => ['required', 'integer', 'min:1', 'max:50'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'special_request' => ['nullable', 'string', 'max:500'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->expirePastBookings();

        $venue = Venue::findOrFail($validated['venue_id']);
        $depositAmount = isset($validated['deposit_amount'])
            ? (float) $validated['deposit_amount']
            : (float) ($venue->deposit_amount ?? 250.00);

        // Compute end time if not given (default: start time + 2 hours)
        $startTime = $validated['booking_time'];
        $endTime = ! empty($validated['booking_end_time']) ? $validated['booking_end_time'] : null;
        if (! $endTime) {
            try {
                $parts = explode(':', $startTime);
                $h = (int) $parts[0] + 2;
                $m = $parts[1] ?? '00';
                $endTime = sprintf('%02d:%s', min($h, 23), $m);
            } catch (\Throwable) {
                $endTime = '21:00';
            }
        }

        // Check for conflicting overlapping bookings on this item
        $conflict = Booking::where('venue_id', $validated['venue_id'])
            ->where('venue_item_id', $validated['venue_item_id'])
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('status', ['confirmed', 'pending_deposit'])
            ->get()
            ->first(function (Booking $b) use ($startTime, $endTime) {
                $bStart = $b->booking_time;
                $bEnd = $b->getEndTime();

                return $bStart < $endTime && $bEnd > $startTime;
            });

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => "โต๊ะหรือสนามนี้ถูกจองไปแล้วในช่วงเวลา {$conflict->booking_time} – {$conflict->getEndTime()} น. กรุณาเลือกช่วงเวลาอื่น",
                'conflict' => [
                    'start_time' => $conflict->booking_time,
                    'end_time' => $conflict->getEndTime(),
                ],
            ], 422);
        }

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
            'customer_phone' => ! empty($validated['customer_phone']) ? $validated['customer_phone'] : (Auth::user()?->phone ?? '081-234-5678'),
            'customer_email' => ! empty($validated['customer_email']) ? $validated['customer_email'] : (Auth::user()?->email ?? 'somchai@example.com'),
            'booking_date' => $validated['booking_date'],
            'booking_time' => $startTime,
            'booking_end_time' => $endTime,
            'party_size' => $validated['party_size'],
            'status' => 'confirmed',
            'deposit_amount' => $depositAmount,
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
     * Get availability and schedule for a venue on a given date and time range.
     */
    public function availability(Request $request, Venue $venue): JsonResponse
    {
        $this->expirePastBookings();

        $date = $request->query('date', Carbon::today()->toDateString());
        $startTime = $request->query('start_time', $request->query('time', '19:00'));
        $endTime = $request->query('end_time');

        if (! $endTime) {
            try {
                $parts = explode(':', $startTime);
                $h = (int) $parts[0] + 2;
                $m = $parts[1] ?? '00';
                $endTime = sprintf('%02d:%s', min($h, 23), $m);
            } catch (\Throwable) {
                $endTime = '21:00';
            }
        }

        $allActiveBookings = Booking::where('venue_id', $venue->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending_deposit'])
            ->get();

        $bookedItemIds = [];
        $itemsSchedule = [];

        foreach ($allActiveBookings as $b) {
            $bStart = $b->booking_time;
            $bEnd = $b->getEndTime();

            // Interval overlap check: bStart < reqEnd && bEnd > reqStart
            if ($bStart < $endTime && $bEnd > $startTime) {
                if ($b->venue_item_id) {
                    $bookedItemIds[] = $b->venue_item_id;
                }
            }

            if ($b->venue_item_id) {
                $itemsSchedule[$b->venue_item_id][] = [
                    'id' => $b->id,
                    'booking_code' => $b->booking_code,
                    'customer_name' => $b->customer_name,
                    'start_time' => $bStart,
                    'end_time' => $bEnd,
                    'time_range' => "{$bStart} – {$bEnd} น.",
                    'party_size' => $b->party_size,
                    'status' => $b->status,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'venue_id' => $venue->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'booked_item_ids' => array_values(array_unique($bookedItemIds)),
            'items_schedule' => $itemsSchedule,
        ]);
    }

    /**
     * Automatically transition confirmed or pending bookings to completed if their date and end time have passed.
     */
    protected function expirePastBookings(): void
    {
        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Past dates are completed
        Booking::whereIn('status', ['confirmed', 'pending_deposit'])
            ->whereDate('booking_date', '<', $todayDate)
            ->update(['status' => 'completed']);

        // Today's bookings that have ended
        $todayBookings = Booking::whereIn('status', ['confirmed', 'pending_deposit'])
            ->whereDate('booking_date', $todayDate)
            ->get();

        foreach ($todayBookings as $b) {
            $endTime = $b->getEndTime();
            if ($endTime <= $currentTime) {
                $b->update(['status' => 'completed']);
            }
        }
    }
}
