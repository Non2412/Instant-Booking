@extends('layouts.app')

@section('title', 'จองทันใจ · จองโต๊ะร้านอาหารและสนามกีฬา')

@section('content')
<!-- ================= DISCOVER SCREEN ================= -->
<div id="screen-discover" class="screen active">
  <div class="hero">
    <svg class="hero-pattern" width="100%" height="100%" viewBox="0 0 1120 420" preserveAspectRatio="none">
      <circle cx="150" cy="80" r="70" fill="none" stroke="#F3EFE3" stroke-width="1.5"/>
      <circle cx="150" cy="80" r="4" fill="#F3EFE3"/>
      <rect x="720" y="40" width="320" height="150" fill="none" stroke="#F3EFE3" stroke-width="1.5"/>
      <line x1="880" y1="40" x2="880" y2="190" stroke="#F3EFE3" stroke-width="1.5"/>
      <circle cx="880" cy="115" r="26" fill="none" stroke="#F3EFE3" stroke-width="1.5"/>
      <circle cx="380" cy="260" r="50" fill="none" stroke="#F3EFE3" stroke-width="1.5"/>
      <circle cx="380" cy="260" r="3" fill="#F3EFE3"/>
    </svg>
    <div class="hero-inner">
      <h1>จองโต๊ะร้านอาหารและสนามกีฬา ได้ในไม่กี่คลิก</h1>
      <p>เลือกร้าน เลือกเวลา เลือกโต๊ะหรือสนามที่ชอบจากผังจริง แล้วรับการยืนยันทันที ไม่ต้องโทรจอง</p>
    </div>
  </div>

  <div class="search-card">
    <div class="search-field">
      <label>ประเภทสถานที่</label>
      <select id="search-type" onchange="filterVenues()">
        <option value="all">ทั้งหมด</option>
        <option value="restaurant">ร้านอาหาร</option>
        <option value="sports">สนามกีฬา</option>
        <option value="cafe">คาเฟ่</option>
        <option value="meeting_room">ห้องประชุม</option>
      </select>
    </div>
    <div class="search-field">
      <label>วันที่</label>
      <input type="date" id="search-date" value="{{ $todayDate }}" onchange="updateBookingDate(this.value)">
    </div>
    <div class="search-field">
      <label>เวลา</label>
      <input type="time" id="search-time" value="19:00" onchange="updateBookingTime(this.value)">
    </div>
    <div class="search-field">
      <label>จำนวนคน</label>
      <input type="number" id="search-guests" value="4" min="1" max="50" onchange="updatePartyCount(parseInt(this.value)||1)">
    </div>
    <button class="btn btn-gold" onclick="filterVenues(); showToast('ค้นหาสถานที่ว่างตามเงื่อนไขเรียบร้อย');">ค้นหา</button>
  </div>

  <div class="wrap section">
    <div class="chip-row">
      <span class="chip active" data-filter="all" onclick="setCategoryChip('all', this)">ทั้งหมด</span>
      <span class="chip" data-filter="restaurant" onclick="setCategoryChip('restaurant', this)">ร้านอาหาร</span>
      <span class="chip" data-filter="cafe" onclick="setCategoryChip('cafe', this)">คาเฟ่</span>
      <span class="chip" data-filter="sports-football" onclick="setCategoryChip('sports-football', this)">สนามฟุตบอล</span>
      <span class="chip" data-filter="sports-badminton" onclick="setCategoryChip('sports-badminton', this)">สนามแบดมินตัน</span>
      <span class="chip" data-filter="sports" onclick="setCategoryChip('sports', this)">สนามกีฬา</span>
      <span class="chip" data-filter="meeting_room" onclick="setCategoryChip('meeting_room', this)">ห้องประชุม</span>
    </div>

    <div class="section-head">
      <h2>แนะนำใกล้คุณ</h2>
      <span class="meta" id="venues-count">{{ $venues->count() }} สถานที่</span>
    </div>

    <div class="venue-grid" id="venue-cards-container">
      @foreach($venues as $venue)
        @php
          $venueTypeKey = $venue->type;
          if (str_contains($venue->name, 'ฟุตบอล')) {
              $venueTypeKey .= ' sports-football';
          } elseif (str_contains($venue->name, 'แบดมินตัน') || str_contains($venue->category_subtitle, 'คอร์ท')) {
              $venueTypeKey .= ' sports-badminton';
          }
          $totalItems = $venue->items->count();
          $takenCount = $venue->bookings->where('booking_date', $todayDate)->whereIn('status', ['confirmed', 'pending_deposit'])->count();
          $freeCount = max(0, $totalItems - $takenCount);
        @endphp
        <div class="venue-card" 
             data-type="{{ $venueTypeKey }}"
             data-venue-id="{{ $venue->id }}"
             onclick="selectAndOpenVenue({{ $venue->id }})">
          <div class="venue-thumb" style="background: {{ $venue->gradient_thumb ?: 'linear-gradient(135deg,#1F3327,#2B4638)' }};">
            <span class="cat-badge">{{ $venue->category_badge }}</span>
          </div>
          <div class="venue-body">
            <div class="name">{{ $venue->name }}</div>
            <div class="meta-line">{{ $venue->category_subtitle }}</div>
            <div class="foot">
              <span class="stars">★ {{ number_format($venue->rating, 1) }} ({{ $venue->reviews_count }})</span>
              @if($freeCount > 0)
                <span class="pill ok">ว่าง {{ $freeCount }} {{ $venue->type === 'sports' ? 'สนาม' : ($venue->type === 'meeting_room' ? 'ห้อง' : 'โต๊ะ') }}</span>
              @else
                <span class="pill no">เต็มช่วงเวลานี้</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- ================= VENUE DETAIL SCREEN ================= -->
<div id="screen-venue" class="screen">
  <div class="wrap venue-header">
    <button class="back-link" onclick="showScreen('discover', document.querySelector('[data-screen=discover]'))">← กลับไปค้นหา</button>
    <div class="venue-title-row">
      <div>
        <h1 id="v-name" class="serif">{{ $selectedVenue->name }}</h1>
        <div class="meta">
          <span id="v-rating">★ {{ number_format($selectedVenue->rating, 1) }} ({{ $selectedVenue->reviews_count }} รีวิว)</span>
          <span id="v-cat">{{ $selectedVenue->category_subtitle }}</span>
          <span id="v-address">{{ $selectedVenue->address }}</span>
          <span id="v-hours">{{ $selectedVenue->opening_hours }}</span>
        </div>
      </div>
      <div class="type-switch">
        <button id="ts-restaurant" class="active" onclick="switchVenueById({{ $venues->firstWhere('type', 'restaurant')?->id ?? 1 }})">ตัวอย่าง: ร้านอาหาร</button>
        <button id="ts-sports" onclick="switchVenueById({{ $venues->firstWhere('type', 'sports')?->id ?? 2 }})">ตัวอย่าง: สนามกีฬา</button>
      </div>
    </div>
  </div>

  <div class="wrap">
    <div class="photo-strip" id="v-photos">
      <div id="v-photo-1" style="background:linear-gradient(135deg,#C1642F,#8C3B24);"></div>
      <div id="v-photo-2" style="background:linear-gradient(135deg,#D8A15A,#A2703A);"></div>
      <div id="v-photo-3" style="background:linear-gradient(135deg,#3F7A52,#255238);"></div>
    </div>

    <div class="venue-layout">
      <div class="venue-main">
        <p class="desc" id="v-desc">{{ $selectedVenue->description }}</p>
        <div class="amenities" id="v-amenities">
          @if($selectedVenue->amenities)
            @foreach($selectedVenue->amenities as $amenity)
              <span class="tag">{{ $amenity }}</span>
            @endforeach
          @endif
        </div>

        <div class="floorplan-panel">
          <h3 id="fp-title">เลือกโต๊ะจากผังร้าน</h3>
          <div class="hint" id="fp-hint">แตะที่โต๊ะที่ว่างเพื่อเลือก · โซนกลางแจ้งรองรับ 2-8 ที่นั่ง</div>
          <div class="legend">
            <span><i style="background:#3F7A52;"></i>ว่าง</span>
            <span><i style="background:var(--gold);"></i>เลือกอยู่</span>
            <span><i style="background:#D8D2C2;"></i>ไม่ว่าง</span>
          </div>
          <div class="fp-grid" id="fp-grid">
            <!-- Dynamic Table Grid populated by JS based on selected venue -->
          </div>
        </div>
      </div>

      <div class="booking-panel">
        <h3>สรุปการจอง</h3>
        <div class="bf">
          <label>วันที่</label>
          <input type="date" id="book-date" value="{{ $todayDate }}" onchange="handleDateChange(this.value)">
        </div>
        <div class="bf">
          <label>เวลา</label>
          <div class="slot-row" id="time-slots">
            <span class="slot" onclick="selectSlot(this, '18:00')">18:00</span>
            <span class="slot active" onclick="selectSlot(this, '19:00')">19:00</span>
            <span class="slot" onclick="selectSlot(this, '19:30')">19:30</span>
            <span class="slot full" title="คิวเต็มแล้ว">20:00</span>
            <span class="slot" onclick="selectSlot(this, '20:30')">20:30</span>
          </div>
        </div>
        <div class="bf">
          <label>จำนวนคน</label>
          <div class="stepper">
            <button type="button" onclick="adjustParty(-1)">−</button>
            <span class="mono" id="party-count" style="font-weight:600; min-width:24px; text-align:center;">4</span>
            <button type="button" onclick="adjustParty(1)">+</button>
          </div>
        </div>
        <div class="bf">
          <label>ชื่อผู้จอง</label>
          <input type="text" id="book-customer-name" value="สมชาย ขยันงาน" placeholder="กรอกชื่อ-นามสกุล">
        </div>
        <div class="bf">
          <label>เบอร์โทรศัพท์</label>
          <input type="text" id="book-customer-phone" value="081-234-5678" placeholder="กรอกเบอร์โทรศัพท์">
        </div>
        <div class="booking-summary">
          <div class="row"><span>สถานที่</span><b id="sum-venue-name">{{ $selectedVenue->name }}</b></div>
          <div class="row"><span>โต๊ะ/สนาม</span><b id="sum-table">T5 (6 ที่นั่ง)</b></div>
          <div class="row"><span>วันเวลา</span><b id="sum-datetime">{{ Carbon\Carbon::parse($todayDate)->addYears(543)->locale('th')->translatedFormat('j M Y') }}, 19:00 น.</b></div>
          <div class="row"><span>มัดจำ</span><b id="sum-deposit">ไม่มีค่ามัดจำ</b></div>
        </div>
        <button class="btn btn-gold" id="btn-submit-booking" style="width:100%; margin-top:16px;" onclick="submitBooking()">
          ยืนยันการจอง
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ================= CONFIRMATION TICKET SCREEN ================= -->
<div id="screen-confirm" class="screen">
  <div class="confirm-wrap">
    <div class="ticket">
      <div class="ticket-top">
        <div class="check">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1F3327" stroke-width="3.2"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <h2>จองสำเร็จแล้ว</h2>
        <p>เราส่งรายละเอียดไปยังอีเมลของคุณแล้ว</p>
      </div>
      <div class="ticket-notch"></div>
      <div class="ticket-body">
        <div class="code mono" id="ticket-code">RSV-2609-7741</div>
        <div class="row"><span>สถานที่</span><span id="ticket-venue">ครัวคุณยาย</span></div>
        <div class="row"><span>โต๊ะ/สนาม</span><span id="ticket-item">T5 · 6 ที่นั่ง</span></div>
        <div class="row"><span>วันที่</span><span id="ticket-date">6 ก.ย. 2569</span></div>
        <div class="row"><span>เวลา</span><span id="ticket-time">19:00 น.</span></div>
        <div class="row"><span>จำนวนคน</span><span id="ticket-party">4 คน</span></div>
        <div class="row"><span>ชื่อผู้จอง</span><span id="ticket-name">สมชาย ขยันงาน</span></div>
      </div>
      <div class="ticket-actions">
        <button class="btn btn-outline btn-sm" onclick="downloadCalendarNotice()">เพิ่มลงปฏิทิน</button>
        <button class="btn btn-gold btn-sm" onclick="showScreen('mybookings', document.querySelector('[data-screen=mybookings]'))">ดูการจองของฉัน</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= MY BOOKINGS SCREEN ================= -->
<div id="screen-mybookings" class="screen">
  <div class="wrap section">
    <h2 style="margin-top:0;">การจองของฉัน</h2>
    <div class="tabs">
      <button class="active" onclick="filterMyBookings('upcoming', this)">กำลังจะถึง</button>
      <button onclick="filterMyBookings('past', this)">ผ่านมาแล้ว</button>
      <button onclick="filterMyBookings('cancelled', this)">ยกเลิก</button>
    </div>
    <div class="booking-list" id="my-bookings-container">
      @forelse($userBookings as $booking)
        @php
          $isPast = Carbon\Carbon::parse($booking->booking_date)->isPast() || $booking->status === 'completed';
          $isCancelled = $booking->status === 'cancelled';
          $tabCategory = $isCancelled ? 'cancelled' : ($isPast ? 'past' : 'upcoming');
          $venue = $booking->venue;
          $thumbBg = $venue?->gradient_thumb ?: 'linear-gradient(135deg,#C1642F,#8C3B24)';
        @endphp
        <div class="bcard my-booking-card" data-tab="{{ $tabCategory }}" style="{{ $tabCategory === 'past' ? 'opacity:.75;' : '' }}">
          <div class="thumb" style="background: {{ $thumbBg }};"></div>
          <div class="info">
            <div class="name">{{ $venue?->name ?? 'ร้านอาหาร' }} · {{ $booking->venueItem?->item_code ?? 'โต๊ะ' }}</div>
            <div class="meta">{{ Carbon\Carbon::parse($booking->booking_date)->addYears(543)->locale('th')->translatedFormat('j M Y') }}, {{ $booking->booking_time }} น. · {{ $booking->party_size }} คน</div>
            <div class="code mono">{{ $booking->booking_code }}</div>
          </div>
          @if($booking->status === 'confirmed')
            <span class="pill ok">ยืนยันแล้ว</span>
          @elseif($booking->status === 'pending_deposit')
            <span class="pill warn">รอชำระมัดจำ</span>
          @elseif($booking->status === 'completed')
            <span class="pill info">เสร็จสิ้น</span>
          @else
            <span class="pill no">ยกเลิกแล้ว</span>
          @endif
          <button class="btn btn-ghost btn-sm" onclick="viewTicketDetails({{ json_encode([
            'code' => $booking->booking_code,
            'venue' => $venue?->name,
            'item' => $booking->venueItem?->item_code . ' · ' . ($booking->venueItem?->capacity_label ?? ''),
            'date' => Carbon\Carbon::parse($booking->booking_date)->addYears(543)->locale('th')->translatedFormat('j M Y'),
            'time' => $booking->booking_time . ' น.',
            'party' => $booking->party_size . ' คน',
            'name' => $booking->customer_name,
          ]) }})">ดูรายละเอียด</button>
        </div>
      @empty
        <div style="text-align:center; padding:50px 0; color:var(--muted);">
          ยังไม่มีประวัติการจอง สามารถเริ่มจองร้านหรือสนามได้ที่หน้าแรก
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- ================= OWNER DASHBOARD SCREEN ================= -->
<div id="screen-owner" class="screen">
  <div class="wrap section">
    <div class="owner-head">
      <h2 style="margin:0;">แดชบอร์ดเจ้าของร้าน · <span id="owner-venue-title">{{ $selectedVenue->name }}</span></h2>
      <button class="btn btn-gold btn-sm" onclick="openAddTableModal()">+ เพิ่มโต๊ะ/ช่วงเวลา</button>
    </div>

    <div class="owner-stats">
      <div class="ostat">
        <div class="label">การจองวันนี้</div>
        <div class="value" id="stat-today-bookings">{{ $stats['today_bookings'] }}</div>
      </div>
      <div class="ostat">
        <div class="label">โต๊ะว่างตอนนี้</div>
        <div class="value" id="stat-free-items">{{ $stats['free_items'] }}</div>
      </div>
      <div class="ostat">
        <div class="label">รอยืนยัน</div>
        <div class="value" id="stat-pending-count">{{ $stats['pending_count'] }}</div>
      </div>
      <div class="ostat">
        <div class="label">รายได้จากการจองวันนี้</div>
        <div class="value" id="stat-today-revenue">{{ $stats['today_revenue'] }}</div>
      </div>
    </div>

    <div class="owner-grid">
      <div class="opanel">
        <div class="opanel-head">
          <h3>รายการจองวันนี้</h3>
          <span style="font-size:12px;color:var(--muted);">{{ Carbon\Carbon::parse($todayDate)->addYears(543)->locale('th')->translatedFormat('j M Y') }}</span>
        </div>
        <div style="overflow-x:auto;">
          <table id="owner-table">
            <thead>
              <tr>
                <th>เวลา</th>
                <th>ลูกค้า</th>
                <th>โต๊ะ/สนาม</th>
                <th>จำนวนคน</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
              </tr>
            </thead>
            <tbody id="owner-bookings-tbody">
              @forelse($ownerBookings as $b)
                <tr id="owner-row-{{ $b->id }}">
                  <td class="mono"><b>{{ $b->booking_time }}</b></td>
                  <td>{{ $b->customer_name }}</td>
                  <td>{{ $b->venueItem?->item_code ?? '-' }}</td>
                  <td>{{ $b->party_size }}</td>
                  <td id="status-cell-{{ $b->id }}">
                    @if($b->status === 'confirmed')
                      <span class="pill ok">ยืนยันแล้ว</span>
                    @elseif($b->status === 'pending_deposit')
                      <span class="pill warn">รอยืนยัน</span>
                    @elseif($b->status === 'cancelled')
                      <span class="pill no">ยกเลิก</span>
                    @else
                      <span class="pill info">เสร็จสิ้น</span>
                    @endif
                  </td>
                  <td>
                    @if($b->status === 'pending_deposit')
                      <button class="btn btn-outline btn-xs" onclick="updateStatus({{ $b->id }}, 'confirmed')">อนุมัติ</button>
                    @elseif($b->status === 'confirmed')
                      <button class="btn btn-ghost btn-xs" onclick="updateStatus({{ $b->id }}, 'completed')">เช็คบิล</button>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align:center; color:var(--muted); padding:30px;">ไม่มีรายการจองในวันนี้</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="opanel">
        <div class="opanel-head">
          <h3>ผังโต๊ะแบบเรียลไทม์</h3>
          <span style="font-size:11.5px; color:var(--muted);">อัปเดตอัตโนมัติ</span>
        </div>
        <div class="mini-fp" id="mini-floorplan">
          <!-- Mini floorplan divs dynamically generated -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: เพิ่มโต๊ะ/ช่วงเวลาจำลอง -->
<div id="modal-add-table" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h3>+ เพิ่มโต๊ะ หรือ ช่วงเวลาใหม่</h3>
      <button class="modal-close" onclick="closeModal('modal-add-table')">×</button>
    </div>
    <div class="modal-body">
      <label>ชื่อโต๊ะ / สนาม</label>
      <input type="text" id="new-item-code" placeholder="เช่น T13, คอร์ท 9">
      <label>จำนวนที่นั่ง / ความจุ</label>
      <input type="number" id="new-item-capacity" value="4" min="1">
      <label>รูปทรงโต๊ะ</label>
      <select id="new-item-shape">
        <option value="round">โต๊ะกลม</option>
        <option value="square">โต๊ะเหลี่ยม / สนาม</option>
      </select>
      <button class="btn btn-gold" style="width:100%; margin-top:8px;" onclick="addNewTable()">บันทึกรายการ</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Application State from Server
const allVenues = @json($venues);
let currentVenueId = {{ $selectedVenue->id }};
let selectedItemId = null;
let selectedItemCode = 'T5';
let selectedItemCapacityLabel = '6 ที่นั่ง';
let currentPartySize = 4;
let currentBookingDate = '{{ $todayDate }}';
let currentBookingTime = '19:00';

// Initialize floorplan & UI on load
document.addEventListener('DOMContentLoaded', () => {
  renderVenueFloorplan(currentVenueId);
  renderMiniFloorplan();
});

// Screen switcher
function showScreen(name, navEl) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  const target = document.getElementById('screen-' + name);
  if (target) {
    target.classList.add('active');
  }
  document.querySelectorAll('.nlink').forEach(n => n.classList.remove('active'));
  if (navEl) {
    navEl.classList.add('active');
  } else {
    const matchedLink = document.querySelector(`.nlink[data-screen="${name}"]`);
    if (matchedLink) matchedLink.classList.add('active');
  }
  window.scrollTo({top: 0, behavior: 'smooth'});
}

// Filter venues in Discover Screen
function filterVenues() {
  const typeFilter = document.getElementById('search-type').value;
  const cards = document.querySelectorAll('.venue-card');
  let visibleCount = 0;

  cards.forEach(card => {
    const cardType = card.getAttribute('data-type') || '';
    let matchesType = (typeFilter === 'all') || cardType.includes(typeFilter);
    if (matchesType) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  document.getElementById('venues-count').textContent = visibleCount + ' สถานที่';
}

function setCategoryChip(filterKey, chipEl) {
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  chipEl.classList.add('active');

  const cards = document.querySelectorAll('.venue-card');
  let visibleCount = 0;

  cards.forEach(card => {
    const cardType = card.getAttribute('data-type') || '';
    let match = (filterKey === 'all') || cardType.includes(filterKey);
    if (match) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  document.getElementById('venues-count').textContent = visibleCount + ' สถานที่';
}

// Select venue and transition to venue detail
function selectAndOpenVenue(venueId) {
  switchVenueById(venueId);
  showScreen('venue', null);
}

function switchVenueById(venueId) {
  const venue = allVenues.find(v => v.id === venueId);
  if (!venue) return;

  currentVenueId = venue.id;

  // Update header and active buttons
  const isRestaurant = (venue.type === 'restaurant' || venue.type === 'cafe');
  document.getElementById('ts-restaurant').classList.toggle('active', isRestaurant);
  document.getElementById('ts-sports').classList.toggle('active', !isRestaurant);

  document.getElementById('v-name').textContent = venue.name;
  document.getElementById('v-rating').textContent = `★ ${parseFloat(venue.rating).toFixed(1)} (${venue.reviews_count} รีวิว)`;
  document.getElementById('v-cat').textContent = venue.category_subtitle;
  document.getElementById('v-address').textContent = venue.address || 'ชัยภูมิ';
  document.getElementById('v-hours').textContent = venue.opening_hours;
  document.getElementById('v-desc').textContent = venue.description;
  document.getElementById('sum-venue-name').textContent = venue.name;

  // Amenities
  const amenContainer = document.getElementById('v-amenities');
  amenContainer.innerHTML = '';
  if (venue.amenities && Array.isArray(venue.amenities)) {
    venue.amenities.forEach(a => {
      const tag = document.createElement('span');
      tag.className = 'tag';
      tag.textContent = a;
      amenContainer.appendChild(tag);
    });
  }

  // Titles
  if (venue.type === 'sports') {
    document.getElementById('fp-title').textContent = 'เลือกสนามจากผังคอร์ท';
    document.getElementById('fp-hint').textContent = 'แตะที่คอร์ทที่ว่างเพื่อเลือก · จองได้ครั้งละ 1 ชั่วโมง';
  } else if (venue.type === 'meeting_room') {
    document.getElementById('fp-title').textContent = 'เลือกห้องประชุมจากผัง';
    document.getElementById('fp-hint').textContent = 'แตะที่ห้องที่ว่างเพื่อเลือก · พร้อมอุปกรณ์ครบครัน';
  } else {
    document.getElementById('fp-title').textContent = 'เลือกโต๊ะจากผังร้าน';
    document.getElementById('fp-hint').textContent = 'แตะที่โต๊ะที่ว่างเพื่อเลือก · โซนกลางแจ้งและห้องแอร์';
  }

  // Photo gradients
  document.getElementById('v-photo-1').style.background = venue.gradient_thumb || 'linear-gradient(135deg,#C1642F,#8C3B24)';

  renderVenueFloorplan(venue.id);
}

// Render interactive floorplan grid
function renderVenueFloorplan(venueId) {
  const venue = allVenues.find(v => v.id === venueId);
  const grid = document.getElementById('fp-grid');
  grid.innerHTML = '';

  if (!venue || !venue.items) return;

  // Pre-taken items simulation for initial render
  const takenCodes = ['T3', 'T7', 'T11', 'คอร์ท 4', 'สนาม 2'];
  let autoSelected = false;

  venue.items.forEach((item, index) => {
    const el = document.createElement('div');
    el.className = 'fp-item';
    if (item.shape === 'round') el.classList.add('round');

    const isTaken = takenCodes.includes(item.item_code);
    if (isTaken) {
      el.classList.add('taken');
      el.innerHTML = `${item.item_code}<small>ไม่ว่าง</small>`;
    } else {
      el.innerHTML = `${item.item_code}<small>${item.capacity_label}</small>`;
      el.onclick = () => pickTableElement(el, item.id, item.item_code, item.capacity_label);

      // Auto-select first available item or T5
      if (!autoSelected && (item.item_code === 'T5' || index === 0)) {
        el.classList.add('selected');
        selectedItemId = item.id;
        selectedItemCode = item.item_code;
        selectedItemCapacityLabel = item.capacity_label;
        document.getElementById('sum-table').textContent = `${item.item_code} (${item.capacity_label})`;
        autoSelected = true;
      }
    }
    grid.appendChild(el);
  });
}

function pickTableElement(el, id, code, capLabel) {
  if (el.classList.contains('taken')) return;
  document.querySelectorAll('#fp-grid .fp-item').forEach(t => t.classList.remove('selected'));
  el.classList.add('selected');
  selectedItemId = id;
  selectedItemCode = code;
  selectedItemCapacityLabel = capLabel;
  document.getElementById('sum-table').textContent = `${code} (${capLabel})`;
}

// Slot and Party controls
function selectSlot(el, timeStr) {
  if (el.classList.contains('full')) return;
  document.querySelectorAll('#time-slots .slot').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  currentBookingTime = timeStr;
  updateDateTimeSummary();
}

function adjustParty(delta) {
  currentPartySize += delta;
  if (currentPartySize < 1) currentPartySize = 1;
  if (currentPartySize > 50) currentPartySize = 50;
  document.getElementById('party-count').textContent = currentPartySize;
  document.getElementById('search-guests').value = currentPartySize;
}

function updatePartyCount(val) {
  currentPartySize = val;
  document.getElementById('party-count').textContent = currentPartySize;
}

function handleDateChange(val) {
  currentBookingDate = val;
  updateDateTimeSummary();
}

function updateBookingDate(val) {
  currentBookingDate = val;
  document.getElementById('book-date').value = val;
  updateDateTimeSummary();
}

function updateBookingTime(val) {
  currentBookingTime = val;
  updateDateTimeSummary();
}

function updateDateTimeSummary() {
  const d = new Date(currentBookingDate);
  const thaiYear = d.getFullYear() + 543;
  const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
  const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;
  document.getElementById('sum-datetime').textContent = `${formattedDate}, ${currentBookingTime} น.`;
}

// Submit Booking via AJAX POST to Laravel Backend
async function submitBooking() {
  const btn = document.getElementById('btn-submit-booking');
  const customerName = document.getElementById('book-customer-name').value.trim() || 'สมชาย ขยันงาน';
  const customerPhone = document.getElementById('book-customer-phone').value.trim() || '081-234-5678';

  if (!selectedItemId) {
    alert('กรุณาคลิกเลือกโต๊ะหรือสนามที่ต้องการจากผัง');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'กำลังยืนยันการจอง...';

  try {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch('{{ route("bookings.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        venue_id: currentVenueId,
        venue_item_id: selectedItemId,
        booking_date: currentBookingDate,
        booking_time: currentBookingTime,
        party_size: currentPartySize,
        customer_name: customerName,
        customer_phone: customerPhone,
      })
    });

    const data = await response.json();

    if (data.success && data.booking) {
      const b = data.booking;
      // Populate Ticket View
      document.getElementById('ticket-code').textContent = b.booking_code;
      document.getElementById('ticket-venue').textContent = b.venue ? b.venue.name : 'ร้านอาหาร';
      document.getElementById('ticket-item').textContent = `${selectedItemCode} · ${selectedItemCapacityLabel}`;
      
      const d = new Date(b.booking_date);
      const thaiYear = d.getFullYear() + 543;
      const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
      const dateText = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;
      
      document.getElementById('ticket-date').textContent = dateText;
      document.getElementById('ticket-time').textContent = `${b.booking_time} น.`;
      document.getElementById('ticket-party').textContent = `${b.party_size} คน`;
      document.getElementById('ticket-name').textContent = b.customer_name;

      // Prepend to My Bookings Container
      prependMyBookingCard(b, dateText);

      // Prepend to Owner Dashboard Table
      prependOwnerRow(b);

      showToast('จองโต๊ะสำเร็จแล้ว! รับรหัสการจองเรียบร้อย');
      showScreen('confirm', null);
    } else {
      alert('เกิดข้อผิดพลาดในการจอง กรุณาลองใหม่อีกครั้ง');
    }
  } catch (err) {
    console.error(err);
    alert('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้');
  } finally {
    btn.disabled = false;
    btn.textContent = 'ยืนยันการจอง';
  }
}

function prependMyBookingCard(b, dateText) {
  const container = document.getElementById('my-bookings-container');
  const card = document.createElement('div');
  card.className = 'bcard my-booking-card';
  card.setAttribute('data-tab', 'upcoming');
  const gradient = (b.venue && b.venue.gradient_thumb) ? b.venue.gradient_thumb : 'linear-gradient(135deg,#C1642F,#8C3B24)';
  
  card.innerHTML = `
    <div class="thumb" style="background:${gradient};"></div>
    <div class="info">
      <div class="name">${b.venue ? b.venue.name : 'ร้านอาหาร'} · ${selectedItemCode}</div>
      <div class="meta">${dateText}, ${b.booking_time} น. · ${b.party_size} คน</div>
      <div class="code mono">${b.booking_code}</div>
    </div>
    <span class="pill ok">ยืนยันแล้ว</span>
    <button class="btn btn-ghost btn-sm" onclick="viewTicketDetails({
      code: '${b.booking_code}',
      venue: '${b.venue ? b.venue.name : ''}',
      item: '${selectedItemCode} · ${selectedItemCapacityLabel}',
      date: '${dateText}',
      time: '${b.booking_time} น.',
      party: '${b.party_size} คน',
      name: '${b.customer_name}'
    })">ดูรายละเอียด</button>
  `;
  container.prepend(card);
}

function prependOwnerRow(b) {
  const tbody = document.getElementById('owner-bookings-tbody');
  const tr = document.createElement('tr');
  tr.id = `owner-row-${b.id}`;
  tr.innerHTML = `
    <td class="mono"><b>${b.booking_time}</b></td>
    <td>${b.customer_name}</td>
    <td>${selectedItemCode}</td>
    <td>${b.party_size}</td>
    <td id="status-cell-${b.id}"><span class="pill ok">ยืนยันแล้ว</span></td>
    <td><button class="btn btn-ghost btn-xs" onclick="updateStatus(${b.id}, 'completed')">เช็คบิล</button></td>
  `;
  tbody.prepend(tr);

  // Update stat counters
  const bookingCountEl = document.getElementById('stat-today-bookings');
  if (bookingCountEl) {
    let current = parseInt(bookingCountEl.textContent) || 0;
    bookingCountEl.textContent = current + 1;
  }
}

// Update status from Owner Dashboard
async function updateStatus(bookingId, newStatus) {
  try {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(`/bookings/${bookingId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ status: newStatus })
    });

    const data = await response.json();
    if (data.success) {
      const cell = document.getElementById(`status-cell-${bookingId}`);
      if (cell) {
        if (newStatus === 'confirmed') cell.innerHTML = '<span class="pill ok">ยืนยันแล้ว</span>';
        else if (newStatus === 'completed') cell.innerHTML = '<span class="pill info">เสร็จสิ้น</span>';
        else if (newStatus === 'cancelled') cell.innerHTML = '<span class="pill no">ยกเลิก</span>';
      }
      showToast('อัปเดตสถานะการจองเรียบร้อยแล้ว');
    }
  } catch (e) {
    console.error(e);
    alert('ไม่สามารถอัปเดตสถานะได้');
  }
}

// View ticket details from My Bookings
function viewTicketDetails(info) {
  document.getElementById('ticket-code').textContent = info.code;
  document.getElementById('ticket-venue').textContent = info.venue;
  document.getElementById('ticket-item').textContent = info.item;
  document.getElementById('ticket-date').textContent = info.date;
  document.getElementById('ticket-time').textContent = info.time;
  document.getElementById('ticket-party').textContent = info.party;
  document.getElementById('ticket-name').textContent = info.name;
  showScreen('confirm', null);
}

// My Bookings tabs filter
function filterMyBookings(tabKey, btnEl) {
  document.querySelectorAll('#screen-mybookings .tabs button').forEach(b => b.classList.remove('active'));
  btnEl.classList.add('active');

  const cards = document.querySelectorAll('.my-booking-card');
  cards.forEach(card => {
    const cardTab = card.getAttribute('data-tab');
    if (tabKey === 'upcoming') {
      card.style.display = (cardTab === 'upcoming') ? 'flex' : 'none';
    } else if (tabKey === 'past') {
      card.style.display = (cardTab === 'past') ? 'flex' : 'none';
    } else if (tabKey === 'cancelled') {
      card.style.display = (cardTab === 'cancelled') ? 'flex' : 'none';
    }
  });
}

// Mini floor plan in Owner Dashboard
function renderMiniFloorplan() {
  const container = document.getElementById('mini-floorplan');
  container.innerHTML = '';
  // 12 mini tables preview
  const statuses = ['free', 'free', 'taken', 'free', 'pending', 'free', 'taken', 'free', 'free', 'free', 'taken', 'free'];
  statuses.forEach((st, i) => {
    const div = document.createElement('div');
    div.textContent = `T${i+1}`;
    if (st === 'taken') div.className = 'taken';
    else if (st === 'pending') div.className = 'pending';
    div.title = `T${i+1} : ${st === 'taken' ? 'มีลูกค้า' : (st === 'pending' ? 'รอยืนยัน' : 'ว่าง')}`;
    container.appendChild(div);
  });
}

function openAddTableModal() {
  document.getElementById('modal-add-table').classList.add('active');
}

function addNewTable() {
  const code = document.getElementById('new-item-code').value.trim() || 'T13';
  const cap = document.getElementById('new-item-capacity').value || '4';
  const shape = document.getElementById('new-item-shape').value || 'round';

  const venue = allVenues.find(v => v.id === currentVenueId);
  if (venue) {
    venue.items.push({
      id: 9999 + Math.floor(Math.random()*1000),
      item_code: code,
      capacity_label: `${cap} ที่นั่ง`,
      capacity: parseInt(cap),
      shape: shape
    });
    renderVenueFloorplan(currentVenueId);
  }

  closeModal('modal-add-table');
  showToast(`เพิ่ม ${code} (${cap} ที่นั่ง) เข้าผังร้านเรียบร้อยแล้ว`);
}

function downloadCalendarNotice() {
  showToast('เพิ่มกำหนดการลงในปฏิทินสำเร็จเรียบร้อย');
}
</script>
@endpush
