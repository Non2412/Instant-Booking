@extends('layouts.app')

@section('title', 'จองทันใจ · จองโต๊ะร้านอาหารและสนามกีฬา')

@push('styles')
<style>
  .time-range-select{
    width:100%; height:40px; padding:0 8px; border:1px solid var(--line); border-radius:8px;
    background:#FCFAF5; font-size:13px; font-weight:600; color:var(--text); font-family:inherit;
    transition:all .15s ease;
  }
  .time-range-select:focus{
    outline:none; border-color:var(--forest); background:#fff;
  }
  .duration-preset-btn{
    padding:5px 11px; border-radius:6px; border:1px solid var(--line); background:#FCFAF5;
    font-size:11.5px; font-weight:600; color:var(--muted); cursor:pointer; transition:all .15s ease;
  }
  .duration-preset-btn:hover{
    border-color:var(--forest); color:var(--forest);
  }
  .duration-preset-btn.active{
    background:var(--forest); color:#fff; border-color:var(--forest);
  }
  .table-schedule-panel{
    background:#FFFFFF; border:1px solid var(--line); border-radius:14px;
    box-shadow:0 3px 12px rgba(20,30,20,.04);
  }
  .ts-hour-block{
    flex:1; height:100%; border-right:1px solid rgba(255,255,255,.3); display:flex;
    align-items:center; justify-content:center; font-size:9.5px; font-weight:700;
    transition:transform .12s ease; cursor:pointer;
  }
  .ts-hour-block:hover{
    transform:scaleY(1.15); z-index:2;
  }
  .ts-hour-block.free{
    background:#3F7A52; color:#fff;
  }
  .ts-hour-block.booked{
    background:#C1642F; color:#fff;
  }
  .fp-item.taken{
    background:#EAE5D8 !important; color:#8C8570 !important; cursor:pointer !important;
    border-color:#D5CDBC !important;
  }
  .fp-item.taken small{
    color:var(--rust) !important; font-weight:700 !important;
  }
</style>
@endpush

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
      <div class="custom-dropdown" id="dropdown-search-type">
        <select id="search-type" onchange="filterVenues()" style="display:none;">
          <option value="all" selected>ทั้งหมด</option>
          <option value="restaurant">ร้านอาหาร</option>
          <option value="sports">สนามกีฬา</option>
          <option value="cafe">คาเฟ่</option>
          <option value="meeting_room">ห้องประชุม</option>
        </select>
        
        <button type="button" class="dropdown-trigger" onclick="toggleDropdown('dropdown-search-type')" aria-haspopup="listbox" aria-expanded="false">
          <span class="dropdown-selected-content">
            <span class="dropdown-icon" id="dt-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
            </span>
            <span class="dropdown-text" id="dt-text">ทั้งหมด</span>
          </span>
          <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
          </svg>
        </button>

        <div class="dropdown-menu" role="listbox">
          <div class="dropdown-item active" data-value="all" data-label="ทั้งหมด" onclick="selectSearchType('all', 'ทั้งหมด', this)">
            <span class="item-left">
              <span class="item-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
              </span>
              <span class="item-name">ทั้งหมด</span>
              <span class="item-badge">120 แห่ง</span>
            </span>
            <svg class="item-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6L9 17l-5-5"/></svg>
          </div>

          <div class="dropdown-item" data-value="restaurant" data-label="ร้านอาหาร" onclick="selectSearchType('restaurant', 'ร้านอาหาร', this)">
            <span class="item-left">
              <span class="item-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
              </span>
              <span class="item-name">ร้านอาหาร</span>
              <span class="item-badge">20 แห่ง</span>
            </span>
            <svg class="item-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6L9 17l-5-5"/></svg>
          </div>

          <div class="dropdown-item" data-value="sports" data-label="สนามกีฬา" onclick="selectSearchType('sports', 'สนามกีฬา', this)">
            <span class="item-left">
              <span class="item-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24M14.83 9.17l4.24-4.24M14.83 14.83l4.24 4.24M9.17 14.83l-4.24 4.24"/></svg>
              </span>
              <span class="item-name">สนามกีฬา</span>
              <span class="item-badge">60 แห่ง</span>
            </span>
            <svg class="item-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6L9 17l-5-5"/></svg>
          </div>

          <div class="dropdown-item" data-value="cafe" data-label="คาเฟ่" onclick="selectSearchType('cafe', 'คาเฟ่', this)">
            <span class="item-left">
              <span class="item-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 8h1a4 4 0 1 1 0 8h-1M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
              </span>
              <span class="item-name">คาเฟ่</span>
              <span class="item-badge">20 แห่ง</span>
            </span>
            <svg class="item-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6L9 17l-5-5"/></svg>
          </div>

          <div class="dropdown-item" data-value="meeting_room" data-label="ห้องประชุม" onclick="selectSearchType('meeting_room', 'ห้องประชุม', this)">
            <span class="item-left">
              <span class="item-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </span>
              <span class="item-name">ห้องประชุม</span>
              <span class="item-badge">20 แห่ง</span>
            </span>
            <svg class="item-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
        </div>
      </div>
    </div>
    <div class="search-field">
      <label>วันที่</label>
      <div class="custom-dropdown" id="dropdown-search-date">
        <input type="hidden" id="search-date" value="{{ $todayDate }}" onchange="updateBookingDate(this.value)">
        <button type="button" class="dropdown-trigger" onclick="toggleDropdown('dropdown-search-date')" aria-haspopup="dialog" aria-expanded="false">
          <span class="dropdown-selected-content">
            <span class="dropdown-icon" id="dd-date-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="dropdown-text" id="dd-date-text">{{ Carbon\Carbon::parse($todayDate)->addYears(543)->locale('th')->translatedFormat('j M Y') }}</span>
          </span>
          <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
          </svg>
        </button>

        <div class="datepicker-menu" id="datepicker-panel" role="dialog">
          <div class="dp-quick-row">
            <button type="button" class="dp-quick-btn" onclick="quickSelectDate(0)">วันนี้</button>
            <button type="button" class="dp-quick-btn" onclick="quickSelectDate(1)">พรุ่งนี้</button>
            <button type="button" class="dp-quick-btn" onclick="quickSelectDate('sat')">เสาร์นี้</button>
          </div>
          <div class="dp-header">
            <button type="button" class="dp-nav-btn" onclick="changeCalendarMonth(-1)">‹</button>
            <span class="dp-month-year" id="dp-month-year-label">กันยายน 2569</span>
            <button type="button" class="dp-nav-btn" onclick="changeCalendarMonth(1)">›</button>
          </div>
          <div class="dp-weekdays">
            <span>อา</span><span>จ</span><span>อ</span><span>พ</span><span>พฤ</span><span>ศ</span><span>ส</span>
          </div>
          <div class="dp-days-grid" id="dp-days-container">
            <!-- Populated dynamically by JS -->
          </div>
        </div>
      </div>
    </div>

    <div class="search-field">
      <label>เวลา</label>
      <div class="custom-dropdown" id="dropdown-search-time">
        <input type="hidden" id="search-time" value="19:00" onchange="updateBookingTime(this.value)">
        <button type="button" class="dropdown-trigger" onclick="toggleDropdown('dropdown-search-time')" aria-haspopup="dialog" aria-expanded="false">
          <span class="dropdown-selected-content">
            <span class="dropdown-icon" id="dd-time-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <span class="dropdown-text" id="dd-time-text">19:00 น.</span>
          </span>
          <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
          </svg>
        </button>

        <div class="timepicker-menu" id="timepicker-panel" role="dialog">
          <!-- Period Switcher Tabs -->
          <div class="tp-tabs">
            <button type="button" class="tp-tab" id="tp-tab-morning" onclick="switchTimeTab('morning')">🌅 เช้า</button>
            <button type="button" class="tp-tab" id="tp-tab-afternoon" onclick="switchTimeTab('afternoon')">☀️ บ่าย</button>
            <button type="button" class="tp-tab active" id="tp-tab-evening" onclick="switchTimeTab('evening')">🌙 เย็น-ค่ำ</button>
          </div>

          <!-- Morning Group (06:00 - 11:30) -->
          <div class="tp-period-group tp-grid" id="tp-group-morning" style="display:none;">
            <span class="tp-slot" onclick="selectTimeSlot('06:00')">06:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('06:30')">06:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('07:00')">07:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('07:30')">07:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('08:00')">08:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('08:30')">08:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('09:00')">09:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('09:30')">09:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('10:00')">10:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('10:30')">10:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('11:00')">11:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('11:30')">11:30</span>
          </div>

          <!-- Afternoon Group (12:00 - 16:30) -->
          <div class="tp-period-group tp-grid" id="tp-group-afternoon" style="display:none;">
            <span class="tp-slot" onclick="selectTimeSlot('12:00')">12:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('12:30')">12:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('13:00')">13:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('13:30')">13:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('14:00')">14:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('14:30')">14:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('15:00')">15:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('15:30')">15:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('16:00')">16:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('16:30')">16:30</span>
          </div>

          <!-- Evening Group (17:00 - 23:30) -->
          <div class="tp-period-group tp-grid" id="tp-group-evening">
            <span class="tp-slot" onclick="selectTimeSlot('17:00')">17:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('17:30')">17:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('18:00')">18:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('18:30')">18:30</span>
            <span class="tp-slot selected" onclick="selectTimeSlot('19:00')">19:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('19:30')">19:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('20:00')">20:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('20:30')">20:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('21:00')">21:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('21:30')">21:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('22:00')">22:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('22:30')">22:30</span>
            <span class="tp-slot" onclick="selectTimeSlot('23:00')">23:00</span>
            <span class="tp-slot" onclick="selectTimeSlot('23:30')">23:30</span>
          </div>

          <!-- Custom Time Bar (Any 24h & minute) -->
          <div class="tp-custom-bar">
            <div class="tp-custom-label">
              <span>หรือระบุเวลาเอง</span>
              <span style="font-size:10.5px; color:var(--muted); font-weight:normal;">(ชม. : นาที)</span>
            </div>
            <div class="tp-custom-inputs">
              <select id="tp-custom-hour" class="tp-custom-select">
                @for($h = 6; $h <= 23; $h++)
                  @php $hPad = str_pad((string)$h, 2, '0', STR_PAD_LEFT); @endphp
                  <option value="{{ $hPad }}" {{ $hPad == '19' ? 'selected' : '' }}>{{ $hPad }}</option>
                @endfor
                @for($h = 0; $h <= 5; $h++)
                  @php $hPad = str_pad((string)$h, 2, '0', STR_PAD_LEFT); @endphp
                  <option value="{{ $hPad }}">{{ $hPad }}</option>
                @endfor
              </select>
              <span style="font-weight:700; color:var(--muted);">:</span>
              <select id="tp-custom-minute" class="tp-custom-select">
                <option value="00" selected>00</option>
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="45">45</option>
              </select>
              <button type="button" class="btn btn-gold btn-sm" style="padding:6px 12px; height:36px; flex-shrink:0;" onclick="applyCustomTime()">ตกลง</button>
            </div>
          </div>
        </div>
      </div>
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
          if ($venue->category_badge === 'สนามฟุตบอล' || str_contains($venue->name, 'ฟุตบอล')) {
              $venueTypeKey .= ' sports-football';
          } elseif ($venue->category_badge === 'สนามแบดมินตัน' || str_contains($venue->name, 'แบดมินตัน') || str_contains($venue->category_subtitle, 'คอร์ท')) {
              $venueTypeKey .= ' sports-badminton';
          } elseif ($venue->type === 'sports') {
              $venueTypeKey .= ' sports-general';
          }
          $totalItems = $venue->items->count();
          $takenCount = $venue->bookings->where('booking_date', $todayDate)->whereIn('status', ['confirmed', 'pending_deposit'])->count();
          $freeCount = max(0, $totalItems - $takenCount);
          $thumbStyle = $venue->image_url
              ? "background-image: url('{$venue->image_url}');"
              : "background: " . ($venue->gradient_thumb ?: 'linear-gradient(135deg,#1F3327,#2B4638)');
        @endphp
        <div class="venue-card" 
             data-type="{{ $venueTypeKey }}"
             data-venue-id="{{ $venue->id }}"
             onclick="selectAndOpenVenue({{ $venue->id }})">
          <div class="venue-thumb" style="{{ $thumbStyle }}">
            <span class="cat-badge">{{ $venue->category_badge }}</span>
          </div>
          <div class="venue-body">
            <div class="name">{{ $venue->name }}</div>
            <div class="meta-line">{{ $venue->category_subtitle }}</div>
            <div class="foot">
              <span class="stars">★ {{ number_format($venue->rating, 1) }} ({{ $venue->reviews_count }})</span>
              <div style="display:inline-flex; align-items:center; gap:6px;">
                <span class="pill" style="background:#F7F3EA; color:#8C5E1B; font-size:11px; font-weight:600;">มัดจำ ฿{{ number_format($venue->deposit_amount) }}</span>
                @if($freeCount > 0)
                  <span class="pill ok">ว่าง {{ $freeCount }} {{ $venue->type === 'sports' ? 'สนาม' : ($venue->type === 'meeting_room' ? 'ห้อง' : 'โต๊ะ') }}</span>
                @else
                  <span class="pill no">เต็ม</span>
                @endif
              </div>
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
      <div id="v-photo-1" style="{{ $selectedVenue->image_url ? "background-image:url('{$selectedVenue->image_url}');" : "background:linear-gradient(135deg,#C1642F,#8C3B24);" }}"></div>
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

        <!-- Table Schedule & Booked Slots Timeline Card -->
        <div class="table-schedule-panel" id="table-schedule-panel" style="margin-top:20px; padding:20px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px; margin-bottom:14px;">
            <div>
              <div style="display:flex; align-items:center; gap:8px;">
                <span id="ts-item-badge" style="background:var(--forest); color:#fff; font-weight:700; font-size:13px; padding:3px 10px; border-radius:6px;">T5</span>
                <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text);" id="ts-item-title">ตารางเวลาการจอง: โต๊ะ 5 (6 ที่นั่ง)</h3>
              </div>
              <div style="font-size:12.5px; color:var(--muted); margin-top:4px;" id="ts-date-label">ประจำวันที่ {{ Carbon\Carbon::parse($todayDate)->addYears(543)->locale('th')->translatedFormat('j M Y') }}</div>
            </div>
            <div id="ts-status-badge">
              <span class="pill ok" style="font-size:12px; padding:5px 12px;">✅ ว่าง พร้อมจองสำหรับช่วงเวลานี้</span>
            </div>
          </div>

          <!-- Visual Hours Timeline Strip -->
          <div style="margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--muted); margin-bottom:6px; font-family:'JetBrains Mono',monospace;">
              <span>10:00</span>
              <span>12:00</span>
              <span>14:00</span>
              <span>16:00</span>
              <span>18:00</span>
              <span>20:00</span>
              <span>22:00</span>
            </div>
            <div id="ts-timeline-bar" style="display:flex; height:24px; border-radius:8px; overflow:hidden; border:1px solid var(--line); background:#EAE5D8;">
              <!-- Dynamically populated timeline segments in JS -->
            </div>
            <div style="display:flex; align-items:center; gap:16px; margin-top:8px; font-size:11.5px; color:var(--muted);">
              <span style="display:inline-flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:3px; background:#3F7A52;"></span> ช่วงเวลาว่าง</span>
              <span style="display:inline-flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:3px; background:#C1642F;"></span> ติดจอง</span>
              <span style="display:inline-flex; align-items:center; gap:6px;"><span style="width:10px; height:10px; border-radius:3px; background:var(--gold);"></span> ช่วงเวลาที่คุณเลือก</span>
            </div>
          </div>

          <!-- Booked Intervals & Free Intervals List -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; background:#FCFAF5; border:1px solid var(--line); border-radius:10px; padding:12px 16px;">
            <div>
              <div style="font-size:12px; font-weight:700; color:var(--rust); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                ช่วงเวลาที่ถูกจองแล้วในวันนี้:
              </div>
              <div id="ts-booked-list" style="font-size:12.5px; color:var(--text); line-height:1.6;">
                <span style="color:var(--muted);">ยังไม่มีการจองสำหรับโต๊ะนี้</span>
              </div>
            </div>
            <div>
              <div style="font-size:12px; font-weight:700; color:var(--forest); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                ช่วงเวลาว่างที่สามารถจองได้:
              </div>
              <div id="ts-free-list" style="font-size:12.5px; color:var(--text); line-height:1.6;">
                <span>เปิดบริการตลอดวัน (10:00 – 22:00 น.)</span>
              </div>
            </div>
          </div>

          <!-- Conflict Suggestion Action Banner -->
          <div id="ts-conflict-banner" style="display:none; margin-top:12px; background:#FFF4E5; border:1px solid #FFD599; border-radius:10px; padding:10px 14px; align-items:center; justify-content:space-between; gap:10px;">
            <div style="font-size:12.5px; color:#8C4B00;">
              ⚠️ โต๊ะนี้ติดจองในช่วงเวลาที่คุณเลือก แนะนำสลับเวลาเป็นช่วงที่ว่างถัดไป
            </div>
            <button type="button" id="btn-quick-shift-time" class="btn btn-gold btn-xs" style="flex-shrink:0;">
              สลับเวลาจอง
            </button>
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
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label style="margin:0;">ช่วงเวลาการจอง (ตั้งแต่ – ถึง)</label>
            <span id="booking-duration-badge" style="font-size:11.5px; color:var(--forest); font-weight:700; background:#EAF3EB; padding:2px 8px; border-radius:999px;">2 ชั่วโมง</span>
          </div>
          
          <!-- Start Time & End Time Range Selectors -->
          <div style="display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:8px; margin-bottom:10px;">
            <div>
              <label style="font-size:11px; color:var(--muted); margin-bottom:3px; display:block; font-weight:600;">เวลาเริ่มต้น</label>
              <select id="book-start-time" class="time-range-select" onchange="handleStartTimeChange(this.value)">
                <!-- Generated options in JS -->
              </select>
            </div>
            <div style="padding-top:16px; font-size:13px; font-weight:700; color:var(--muted); text-align:center;">ถึง</div>
            <div>
              <label style="font-size:11px; color:var(--muted); margin-bottom:3px; display:block; font-weight:600;">เวลาสิ้นสุด</label>
              <select id="book-end-time" class="time-range-select" onchange="handleEndTimeChange(this.value)">
                <!-- Generated options in JS -->
              </select>
            </div>
          </div>

          <!-- Quick Duration Presets -->
          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px;">
            <button type="button" class="duration-preset-btn active" data-hours="2" onclick="selectDurationPreset(2, this)">2 ชม. (มาตรฐาน)</button>
            <button type="button" class="duration-preset-btn" data-hours="1" onclick="selectDurationPreset(1, this)">1 ชม.</button>
            <button type="button" class="duration-preset-btn" data-hours="1.5" onclick="selectDurationPreset(1.5, this)">1.5 ชม.</button>
            <button type="button" class="duration-preset-btn" data-hours="3" onclick="selectDurationPreset(3, this)">3 ชม.</button>
          </div>

          <!-- Quick Start Slots -->
          <label style="font-size:11px; color:var(--muted); margin-bottom:4px; display:block; font-weight:600;">ทางลัดเลือกเวลาเริ่ม:</label>
          <div class="slot-row" id="time-slots">
            <span class="slot" onclick="selectQuickSlot('17:00')">17:00</span>
            <span class="slot" onclick="selectQuickSlot('18:00')">18:00</span>
            <span class="slot active" onclick="selectQuickSlot('19:00')">19:00</span>
            <span class="slot" onclick="selectQuickSlot('19:30')">19:30</span>
            <span class="slot" onclick="selectQuickSlot('20:00')">20:00</span>
            <span class="slot" onclick="selectQuickSlot('20:30')">20:30</span>
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
          <input type="text" id="book-customer-name" value="{{ Auth::check() ? Auth::user()->name : 'สมชาย ขยันงาน' }}" placeholder="กรอกชื่อ-นามสกุล">
        </div>
        <div class="bf">
          <label>เบอร์โทรศัพท์</label>
          <input type="text" id="book-customer-phone" value="{{ Auth::check() ? (Auth::user()->phone ?? '081-234-5678') : '081-234-5678' }}" placeholder="กรอกเบอร์โทรศัพท์">
        </div>
        <div class="booking-summary">
          <div class="row"><span>สถานที่</span><b id="sum-venue-name">{{ $selectedVenue->name }}</b></div>
          <div class="row"><span>โต๊ะ/สนาม</span><b id="sum-table">T5 (6 ที่นั่ง)</b></div>
          <div class="row"><span>วันเวลา</span><b id="sum-datetime">{{ Carbon\Carbon::parse($todayDate)->addYears(543)->locale('th')->translatedFormat('j M Y') }}, 19:00 – 21:00 น.</b></div>
          <div class="row"><span>มัดจำจอง</span><b id="sum-deposit" style="color:var(--forest);">฿{{ number_format($selectedVenue->deposit_amount ?? 200) }} (มัดจำล่วงหน้า)</b></div>
        </div>
        <button class="btn btn-gold" id="btn-submit-booking" style="width:100%; margin-top:16px;" onclick="openPaymentModal()">
          ชำระมัดจำ (฿{{ number_format($selectedVenue->deposit_amount ?? 200) }}) & ยืนยันการจอง
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
        <div class="row"><span>ค่ามัดจำ</span><span class="pill ok" id="ticket-deposit">฿{{ number_format($selectedVenue->deposit_amount ?? 200) }} (ชำระแล้วผ่าน PromptPay QR)</span></div>
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
            <div class="meta">{{ Carbon\Carbon::parse($booking->booking_date)->addYears(543)->locale('th')->translatedFormat('j M Y') }}, {{ $booking->getTimeRangeLabel() }} · {{ $booking->party_size }} คน</div>
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
          @if($booking->deposit_amount > 0)
            <span class="pill" style="background:#FAF3E3; color:#9B6C1E; font-size:11px; font-weight:600;">มัดจำ ฿{{ number_format($booking->deposit_amount) }}</span>
          @endif
          <button class="btn btn-ghost btn-sm" onclick="viewTicketDetails({{ json_encode([
            'code' => $booking->booking_code,
            'venue' => $venue?->name,
            'item' => $booking->venueItem?->item_code . ' · ' . ($booking->venueItem?->capacity_label ?? ''),
            'date' => Carbon\Carbon::parse($booking->booking_date)->addYears(543)->locale('th')->translatedFormat('j M Y'),
            'time' => $booking->getTimeRangeLabel(),
            'party' => $booking->party_size . ' คน',
            'name' => $booking->customer_name,
            'deposit' => '฿' . number_format($booking->deposit_amount) . ' (ชำระแล้วผ่าน PromptPay QR)',
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
                <th>มัดจำ</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
              </tr>
            </thead>
            <tbody id="owner-bookings-tbody">
              @forelse($ownerBookings as $b)
                <tr id="owner-row-{{ $b->id }}">
                  <td class="mono"><b>{{ $b->booking_time }} – {{ $b->getEndTime() }}</b></td>
                  <td>{{ $b->customer_name }}</td>
                  <td>{{ $b->venueItem?->item_code ?? '-' }}</td>
                  <td>{{ $b->party_size }}</td>
                  <td><b class="mono" style="color:var(--forest);">฿{{ number_format($b->deposit_amount) }}</b></td>
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
                  <td colspan="7" style="text-align:center; color:var(--muted); padding:30px;">ไม่มีรายการจองในวันนี้</td>
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

<!-- Modal: ชำระเงินค่ามัดจำ (Thai QR Payment / PromptPay) -->
<div id="modal-payment" class="modal-overlay">
  <div class="modal" style="max-width:480px; max-height:92vh; overflow-y:auto;">
    <div class="modal-header" style="background:#FAF7EE; border-bottom:1px solid var(--line);">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:var(--forest); color:#fff; flex-shrink:0;">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </span>
        <div>
          <h3 style="margin:0; font-size:16px;">ชำระเงินค่ามัดจำ / ค่าจองโต๊ะ</h3>
          <span style="font-size:11.5px; color:var(--muted);">ระบบชำระเงินผ่าน PromptPay QR Code</span>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-payment')">×</button>
    </div>

    <div class="modal-body" style="padding:20px;">
      <!-- Venue Booking Summary Header in Modal -->
      <div style="background:#F5EFE1; border:1px solid #E5DEC9; border-radius:12px; padding:14px 16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
          <div>
            <div style="font-weight:700; font-size:15px; color:var(--text);" id="pay-venue-name">{{ $selectedVenue->name }}</div>
            <div style="font-size:12px; color:var(--muted); margin-top:2px;" id="pay-booking-details">T5 (6 ที่นั่ง) · วันนี้, 19:00 น. · 4 ท่าน</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:11px; color:var(--muted); font-weight:600;">ยอดมัดจำที่ต้องชำระ</div>
            <div style="font-size:20px; font-weight:700; color:var(--rust); font-family:'JetBrains Mono',monospace;" id="pay-deposit-amount">฿{{ number_format($selectedVenue->deposit_amount ?? 200, 2) }}</div>
          </div>
        </div>
        <div style="font-size:11.5px; color:#6B6454; border-top:1px dashed #D6CDBC; padding-top:8px; margin-top:6px; display:flex; align-items:center; gap:6px;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
          <span>ค่ามัดจำจะนำไปหักลบกับยอดบิลค่าอาหาร/บริการ ณ วันที่เข้าใช้บริการ</span>
        </div>
      </div>

      <!-- QR Code Presentation Card -->
      <div style="background:#FFFFFF; border:1.5px solid var(--forest); border-radius:14px; padding:16px; text-align:center; box-shadow:0 8px 24px rgba(20,30,20,.06);">
        <div style="display:inline-flex; align-items:center; gap:6px; background:#1F3327; color:#fff; font-size:11px; font-weight:700; padding:4px 12px; border-radius:999px; margin-bottom:12px; letter-spacing:0.3px;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          THAI QR PAYMENT · PROMPTPAY
        </div>

        <div style="max-width:280px; margin:0 auto 12px; border-radius:10px; overflow:hidden; border:1px solid #E5E0D5; background:#FAF8F4; padding:6px;">
          <img src="{{ asset('images/qr-payment.jpg') }}" alt="Thai QR Payment PromptPay" style="width:100%; height:auto; display:block; border-radius:6px; object-fit:contain;">
        </div>

        <!-- Bank Details & Copy -->
        <div style="background:#FAF8F2; border:1px solid var(--line); border-radius:10px; padding:10px 14px; text-align:left; font-size:12.5px; margin-bottom:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
            <span style="color:var(--muted);">ธนาคาร:</span>
            <span style="font-weight:600; color:#137E3A; display:inline-flex; align-items:center; gap:5px;">
              <span style="width:8px; height:8px; border-radius:50%; background:#137E3A;"></span> ธนาคารกสิกรไทย (K-Bank)
            </span>
          </div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
            <span style="color:var(--muted);">ชื่อบัญชี:</span>
            <span style="font-weight:600; color:var(--text);">นาย สิทธิรัช พรหมคุณ</span>
          </div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:var(--muted);">เลขที่อ้างอิง / PromptPay:</span>
            <span style="font-weight:700; font-family:'JetBrains Mono',monospace; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
              <span id="copy-ref-text">004999098316259</span>
              <button type="button" onclick="copyRefCode()" style="background:#fff; border:1px solid var(--line); border-radius:4px; padding:2px 7px; font-size:11px; color:var(--forest); cursor:pointer; font-weight:600; transition:all .15s ease;">คัดลอก</button>
            </span>
          </div>
        </div>

        <!-- Countdown Timer -->
        <div style="display:flex; align-items:center; justify-content:center; gap:6px; font-size:12px; color:var(--rust); font-weight:600;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>กรุณาชำระเงินภายใน: <b id="pay-timer" style="font-family:'JetBrains Mono',monospace; font-size:14px;">14:59</b> นาที</span>
        </div>
      </div>

      <!-- Slip Attachment -->
      <div style="margin-top:14px;">
        <label style="font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; display:flex; justify-content:space-between;">
          <span>แนบหลักฐานการโอนเงิน / สลิป (ระบบตรวจสอบอัตโนมัติ)</span>
          <span style="color:var(--forest); font-weight:normal;">ไม่บังคับ</span>
        </label>
        <div id="slip-dropzone" onclick="document.getElementById('slip-input').click()" style="border:2px dashed var(--line); border-radius:10px; padding:12px; text-align:center; background:#FCFAF5; cursor:pointer; transition:all .15s ease;">
          <input type="file" id="slip-input" accept="image/*" style="display:none;" onchange="handleSlipSelected(this)">
          <div id="slip-prompt" style="font-size:12px; color:var(--muted); display:flex; align-items:center; justify-content:center; gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
            <span>คลิกเพื่อแนบรูปสลิปจากอุปกรณ์ (PNG, JPG)</span>
          </div>
          <div id="slip-preview-box" style="display:none; align-items:center; justify-content:space-between; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
              <img id="slip-preview-img" src="" alt="Slip Preview" style="width:38px; height:38px; object-fit:cover; border-radius:6px; border:1px solid var(--line);">
              <div style="font-size:12px; text-align:left;">
                <div style="font-weight:600; color:var(--forest);" id="slip-filename">slip.jpg</div>
                <div style="color:var(--muted); font-size:11px;">แนบสลิปเรียบร้อยแล้ว</div>
              </div>
            </div>
            <button type="button" onclick="event.stopPropagation(); removeSlip();" style="background:none; border:none; color:var(--rust); font-size:12px; cursor:pointer; font-weight:600; text-decoration:underline;">ลบออก</button>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="display:flex; gap:10px; margin-top:18px;">
        <button type="button" class="btn btn-outline" style="flex:1;" onclick="closeModal('modal-payment')">ยกเลิก</button>
        <button type="button" class="btn btn-gold" id="btn-confirm-payment" style="flex:2; font-weight:700;" onclick="confirmDepositAndBook()">
          ยืนยันการชำระเงินและรับใบจอง
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Application State from Server
const allVenues = @json($venues);
let currentVenueId = {{ $selectedVenue->id }};
let currentVenueDeposit = {{ $selectedVenue->deposit_amount ?? 200 }};
let selectedItemId = null;
let selectedItemCode = 'T5';
let selectedItemCapacityLabel = '6 ที่นั่ง';
let currentPartySize = 4;
let currentBookingDate = '{{ $todayDate }}';
let currentBookingTime = '19:00';
let currentBookingEndTime = '21:00';
let currentDurationHours = 2;
let currentBookedItemIds = [];
let currentItemsSchedule = {};
let currentVenueOpeningHours = '{{ $selectedVenue->opening_hours }}';
let paymentCountdownInterval = null;

// Time & Duration Helpers
function timeToMinutes(timeStr) {
  if (!timeStr) return 0;
  const parts = timeStr.split(':');
  return parseInt(parts[0], 10) * 60 + parseInt(parts[1] || 0, 10);
}

function minutesToTime(mins) {
  const h = Math.floor(mins / 60) % 24;
  const m = mins % 60;
  return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
}

function addHoursToTime(timeStr, hours) {
  const mins = timeToMinutes(timeStr) + Math.round(hours * 60);
  return minutesToTime(mins);
}

function populateTimeOptions() {
  const startSelect = document.getElementById('book-start-time');
  const endSelect = document.getElementById('book-end-time');
  if (!startSelect || !endSelect) return;

  startSelect.innerHTML = '';
  endSelect.innerHTML = '';

  for (let m = 8 * 60; m <= 23 * 60 + 30; m += 30) {
    const t = minutesToTime(m);
    const optStart = document.createElement('option');
    optStart.value = t;
    optStart.textContent = `${t} น.`;
    if (t === currentBookingTime) optStart.selected = true;
    startSelect.appendChild(optStart);

    const optEnd = document.createElement('option');
    optEnd.value = t;
    optEnd.textContent = `${t} น.`;
    if (t === currentBookingEndTime) optEnd.selected = true;
    endSelect.appendChild(optEnd);
  }
}

function handleStartTimeChange(val) {
  currentBookingTime = val;
  currentBookingEndTime = addHoursToTime(val, currentDurationHours);

  const endSelect = document.getElementById('book-end-time');
  if (endSelect) endSelect.value = currentBookingEndTime;

  document.querySelectorAll('#time-slots .slot').forEach(s => {
    s.classList.toggle('active', s.textContent.trim() === val);
  });

  const searchInput = document.getElementById('search-time');
  if (searchInput) searchInput.value = val;
  const textEl = document.getElementById('dd-time-text');
  if (textEl) textEl.textContent = `${val} น.`;

  updateDateTimeSummary();
  fetchVenueAvailability();
}

function handleEndTimeChange(val) {
  const startMins = timeToMinutes(currentBookingTime);
  let endMins = timeToMinutes(val);

  if (endMins <= startMins) {
    endMins = startMins + 60;
    val = minutesToTime(endMins);
    const endSelect = document.getElementById('book-end-time');
    if (endSelect) endSelect.value = val;
  }

  currentBookingEndTime = val;
  const durationDiff = (endMins - startMins) / 60;
  currentDurationHours = durationDiff;

  const durationBadge = document.getElementById('booking-duration-badge');
  if (durationBadge) {
    durationBadge.textContent = `${durationDiff % 1 === 0 ? durationDiff : durationDiff.toFixed(1)} ชั่วโมง`;
  }

  document.querySelectorAll('.duration-preset-btn').forEach(btn => {
    const h = parseFloat(btn.getAttribute('data-hours'));
    btn.classList.toggle('active', h === durationDiff);
  });

  updateDateTimeSummary();
  fetchVenueAvailability();
}

function selectDurationPreset(hours, btn) {
  currentDurationHours = hours;
  currentBookingEndTime = addHoursToTime(currentBookingTime, hours);

  const endSelect = document.getElementById('book-end-time');
  if (endSelect) endSelect.value = currentBookingEndTime;

  const durationBadge = document.getElementById('booking-duration-badge');
  if (durationBadge) {
    durationBadge.textContent = `${hours} ชั่วโมง`;
  }

  document.querySelectorAll('.duration-preset-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  updateDateTimeSummary();
  fetchVenueAvailability();
}

function selectQuickSlot(timeStr) {
  currentBookingTime = timeStr;
  currentBookingEndTime = addHoursToTime(timeStr, currentDurationHours);

  const startSelect = document.getElementById('book-start-time');
  if (startSelect) startSelect.value = timeStr;

  const endSelect = document.getElementById('book-end-time');
  if (endSelect) endSelect.value = currentBookingEndTime;

  document.querySelectorAll('#time-slots .slot').forEach(s => {
    s.classList.toggle('active', s.textContent.trim() === timeStr);
  });

  const searchInput = document.getElementById('search-time');
  if (searchInput) searchInput.value = timeStr;
  const textEl = document.getElementById('dd-time-text');
  if (textEl) textEl.textContent = `${timeStr} น.`;

  updateDateTimeSummary();
  fetchVenueAvailability();
}

// Thai Date & Time Helpers
const thaiMonthFull = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
const thaiMonthShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

let calDate = new Date('{{ $todayDate }}');
if (isNaN(calDate.getTime())) calDate = new Date();
let calViewingYear = calDate.getFullYear();
let calViewingMonth = calDate.getMonth();

function initCustomPickers() {
  renderCalendarDays();
}

function changeCalendarMonth(delta) {
  calViewingMonth += delta;
  if (calViewingMonth < 0) {
    calViewingMonth = 11;
    calViewingYear--;
  } else if (calViewingMonth > 11) {
    calViewingMonth = 0;
    calViewingYear++;
  }
  renderCalendarDays();
}

function renderCalendarDays() {
  const labelEl = document.getElementById('dp-month-year-label');
  const container = document.getElementById('dp-days-container');
  if (!labelEl || !container) return;

  labelEl.textContent = `${thaiMonthFull[calViewingMonth]} ${calViewingYear + 543}`;
  container.innerHTML = '';

  const firstDayIndex = new Date(calViewingYear, calViewingMonth, 1).getDay(); // 0 = Sun
  const totalDays = new Date(calViewingYear, calViewingMonth + 1, 0).getDate();
  const prevMonthDays = new Date(calViewingYear, calViewingMonth, 0).getDate();

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const selectedD = new Date(currentBookingDate);
  selectedD.setHours(0, 0, 0, 0);

  // Previous month padding days
  for (let i = firstDayIndex - 1; i >= 0; i--) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dp-day-cell other-month disabled';
    btn.textContent = prevMonthDays - i;
    btn.disabled = true;
    container.appendChild(btn);
  }

  // Days of current month
  for (let day = 1; day <= totalDays; day++) {
    const thisDate = new Date(calViewingYear, calViewingMonth, day);
    thisDate.setHours(0, 0, 0, 0);

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dp-day-cell';
    btn.textContent = day;

    const isToday = thisDate.getTime() === today.getTime();
    const isSelected = thisDate.getTime() === selectedD.getTime();
    const isPast = thisDate.getTime() < today.getTime();

    if (isToday) btn.classList.add('today');
    if (isSelected) btn.classList.add('selected');

    if (isPast) {
      btn.classList.add('disabled');
      btn.disabled = true;
    } else {
      btn.onclick = () => {
        const yStr = calViewingYear;
        const mStr = String(calViewingMonth + 1).padStart(2, '0');
        const dStr = String(day).padStart(2, '0');
        const dateStr = `${yStr}-${mStr}-${dStr}`;
        selectCalendarDate(dateStr, day, calViewingMonth, calViewingYear);
      };
    }

    container.appendChild(btn);
  }

  // Next month padding days to fill 35 or 42 grid
  const totalRendered = firstDayIndex + totalDays;
  const nextPadding = (totalRendered <= 35) ? (35 - totalRendered) : (42 - totalRendered);
  for (let d = 1; d <= nextPadding; d++) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dp-day-cell other-month disabled';
    btn.textContent = d;
    btn.disabled = true;
    container.appendChild(btn);
  }
}

function selectCalendarDate(dateStr, day, monthIndex, year) {
  currentBookingDate = dateStr;

  const hiddenSearch = document.getElementById('search-date');
  if (hiddenSearch) hiddenSearch.value = dateStr;

  const hiddenBook = document.getElementById('book-date');
  if (hiddenBook) hiddenBook.value = dateStr;

  const textEl = document.getElementById('dd-date-text');
  if (textEl) {
    textEl.textContent = `${day} ${thaiMonthShort[monthIndex]} ${year + 543}`;
  }

  updateDateTimeSummary();
  closeAllDropdowns();
  renderCalendarDays();
}

function quickSelectDate(type) {
  const d = new Date();
  d.setHours(0, 0, 0, 0);

  if (type === 1) {
    // Tomorrow
    d.setDate(d.getDate() + 1);
  } else if (type === 'sat') {
    // Upcoming Saturday
    const day = d.getDay();
    const diff = (6 - day + 7) % 7 || 7;
    d.setDate(d.getDate() + diff);
  }

  const yStr = d.getFullYear();
  const mStr = String(d.getMonth() + 1).padStart(2, '0');
  const dStr = String(d.getDate()).padStart(2, '0');
  const dateStr = `${yStr}-${mStr}-${dStr}`;

  calViewingYear = d.getFullYear();
  calViewingMonth = d.getMonth();

  selectCalendarDate(dateStr, d.getDate(), d.getMonth(), d.getFullYear());
}

function switchTimeTab(period) {
  document.querySelectorAll('.tp-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tp-period-group').forEach(g => g.style.display = 'none');

  const tab = document.getElementById(`tp-tab-${period}`);
  const group = document.getElementById(`tp-group-${period}`);
  if (tab) tab.classList.add('active');
  if (group) group.style.display = 'grid';
}

function selectTimeSlot(timeStr) {
  currentBookingTime = timeStr;

  const hiddenInput = document.getElementById('search-time');
  if (hiddenInput) hiddenInput.value = timeStr;

  const textEl = document.getElementById('dd-time-text');
  if (textEl) textEl.textContent = `${timeStr} น.`;

  // Update active slot in timepicker
  document.querySelectorAll('#timepicker-panel .tp-slot').forEach(s => {
    s.classList.toggle('selected', s.textContent.trim() === timeStr);
  });

  // Switch to the correct period tab
  const parts = timeStr.split(':');
  const h = parts[0];
  const m = parts[1] || '00';
  const hourNum = parseInt(h, 10);
  if (hourNum < 12) {
    switchTimeTab('morning');
  } else if (hourNum < 17) {
    switchTimeTab('afternoon');
  } else {
    switchTimeTab('evening');
  }

  // Update custom inputs to match
  const hourSelect = document.getElementById('tp-custom-hour');
  const minSelect = document.getElementById('tp-custom-minute');
  if (hourSelect) hourSelect.value = h.padStart(2, '0');
  if (minSelect) minSelect.value = m;

  // Also sync with booking panel slot pills if matching
  document.querySelectorAll('#time-slots .slot').forEach(s => {
    s.classList.toggle('active', s.textContent.trim() === timeStr);
  });

  updateDateTimeSummary();
  closeAllDropdowns();
}

function applyCustomTime() {
  const h = document.getElementById('tp-custom-hour').value;
  const m = document.getElementById('tp-custom-minute').value;
  const timeStr = `${h}:${m}`;
  selectTimeSlot(timeStr);
}

// Initialize floorplan & UI on load
document.addEventListener('DOMContentLoaded', () => {
  populateTimeOptions();
  renderMiniFloorplan();
  initCustomPickers();
  fetchVenueAvailability();
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

// Handle selection in Custom Dropdown
function selectSearchType(value, label, itemEl) {
  const select = document.getElementById('search-type');
  if (select) {
    select.value = value;
  }

  const textEl = document.getElementById('dt-text');
  const iconEl = document.getElementById('dt-icon');
  if (textEl) textEl.textContent = label;
  if (iconEl && itemEl) {
    const itemIcon = itemEl.querySelector('.item-icon');
    if (itemIcon) iconEl.innerHTML = itemIcon.innerHTML;
  }

  document.querySelectorAll('#dropdown-search-type .dropdown-item').forEach(el => {
    el.classList.remove('active');
  });
  if (itemEl) {
    itemEl.classList.add('active');
  }

  const dropdown = document.getElementById('dropdown-search-type');
  if (dropdown) {
    dropdown.classList.remove('open');
    dropdown.querySelector('.dropdown-trigger')?.setAttribute('aria-expanded', 'false');
  }

  // Sync active category chip if exact match
  const matchingChip = document.querySelector(`.chip[data-filter="${value}"]`);
  if (matchingChip) {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    matchingChip.classList.add('active');
  }

  filterVenues();
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
    let match = false;
    if (filterKey === 'all') {
      match = true;
    } else if (filterKey === 'sports') {
      match = cardType.includes('sports-general') || (cardType.includes('sports') && !cardType.includes('sports-football') && !cardType.includes('sports-badminton'));
    } else {
      match = cardType.includes(filterKey);
    }

    if (match) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  document.getElementById('venues-count').textContent = visibleCount + ' สถานที่';

  // Sync with custom dropdown
  let dropdownVal = filterKey;
  if (filterKey.startsWith('sports')) {
    dropdownVal = 'sports';
  }
  const matchingItem = document.querySelector(`#dropdown-search-type .dropdown-item[data-value="${dropdownVal}"]`);
  if (matchingItem) {
    const label = matchingItem.getAttribute('data-label');
    const itemIcon = matchingItem.querySelector('.item-icon');
    const textEl = document.getElementById('dt-text');
    const iconEl = document.getElementById('dt-icon');
    if (textEl && label) textEl.textContent = label;
    if (iconEl && itemIcon) iconEl.innerHTML = itemIcon.innerHTML;

    document.querySelectorAll('#dropdown-search-type .dropdown-item').forEach(el => el.classList.remove('active'));
    matchingItem.classList.add('active');

    const select = document.getElementById('search-type');
    if (select) select.value = dropdownVal;
  }
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
  const deposit = venue.deposit_amount ? parseFloat(venue.deposit_amount) : 250;
  currentVenueDeposit = deposit;
  const depositText = `฿${deposit.toLocaleString()} (มัดจำล่วงหน้า)`;
  const depositEl = document.getElementById('sum-deposit');
  if (depositEl) depositEl.textContent = depositText;
  const btnEl = document.getElementById('btn-submit-booking');
  if (btnEl) btnEl.textContent = `ชำระมัดจำ (฿${deposit.toLocaleString()}) & ยืนยันการจอง`;

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

  // Photo
  const photo1 = document.getElementById('v-photo-1');
  if (photo1) {
    if (venue.image_url) {
      photo1.style.backgroundImage = `url('${venue.image_url}')`;
      photo1.style.backgroundSize = 'cover';
      photo1.style.backgroundPosition = 'center';
    } else {
      photo1.style.backgroundImage = '';
      photo1.style.background = venue.gradient_thumb || 'linear-gradient(135deg,#C1642F,#8C3B24)';
    }
  }

  currentVenueOpeningHours = venue.opening_hours;
  fetchVenueAvailability();
}

// Fetch availability and schedule data from server
async function fetchVenueAvailability() {
  try {
    const url = `/venues/${currentVenueId}/availability?date=${encodeURIComponent(currentBookingDate)}&start_time=${encodeURIComponent(currentBookingTime)}&end_time=${encodeURIComponent(currentBookingEndTime)}`;
    const res = await fetch(url, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) return;
    const data = await res.json();
    if (data.success) {
      currentBookedItemIds = data.booked_item_ids || [];
      currentItemsSchedule = data.items_schedule || {};
      renderVenueFloorplan(currentVenueId);
      updateTableScheduleCard();
    }
  } catch (err) {
    console.error('Error fetching availability:', err);
  }
}

// Render interactive floorplan grid with real booked status
function renderVenueFloorplan(venueId) {
  const venue = allVenues.find(v => v.id === venueId);
  const grid = document.getElementById('fp-grid');
  if (!grid) return;
  grid.innerHTML = '';

  if (!venue || !venue.items) return;

  let hasSelected = false;

  venue.items.forEach((item, index) => {
    const el = document.createElement('div');
    el.className = 'fp-item';
    if (item.shape === 'round') el.classList.add('round');

    const isTaken = currentBookedItemIds.includes(item.id);
    if (isTaken) {
      el.classList.add('taken');
      el.innerHTML = `${item.item_code}<small>ติดจอง</small>`;
    } else {
      el.innerHTML = `${item.item_code}<small>${item.capacity_label}</small>`;
    }

    el.onclick = () => pickTableElement(el, item.id, item.item_code, item.capacity_label);

    // Keep selected if matches selectedItemId
    if (selectedItemId === item.id) {
      el.classList.add('selected');
      hasSelected = true;
    }

    grid.appendChild(el);
  });

  // If nothing is selected, select first available item or first item
  if (!hasSelected && venue.items.length > 0) {
    const firstFree = venue.items.find(it => !currentBookedItemIds.includes(it.id)) || venue.items[0];
    selectedItemId = firstFree.id;
    selectedItemCode = firstFree.item_code;
    selectedItemCapacityLabel = firstFree.capacity_label;
    const sumTable = document.getElementById('sum-table');
    if (sumTable) sumTable.textContent = `${firstFree.item_code} (${firstFree.capacity_label})`;

    const items = grid.querySelectorAll('.fp-item');
    const freeIndex = venue.items.indexOf(firstFree);
    if (items[freeIndex]) items[freeIndex].classList.add('selected');
  }

  updateTableScheduleCard();
}

function pickTableElement(el, id, code, capLabel) {
  document.querySelectorAll('#fp-grid .fp-item').forEach(t => t.classList.remove('selected'));
  el.classList.add('selected');
  selectedItemId = id;
  selectedItemCode = code;
  selectedItemCapacityLabel = capLabel;
  const sumTable = document.getElementById('sum-table');
  if (sumTable) sumTable.textContent = `${code} (${capLabel})`;
  updateTableScheduleCard();
}

function updateTableScheduleCard() {
  const panel = document.getElementById('table-schedule-panel');
  if (!panel) return;

  const itemBadge = document.getElementById('ts-item-badge');
  const itemTitle = document.getElementById('ts-item-title');
  const dateLabel = document.getElementById('ts-date-label');
  const statusBadge = document.getElementById('ts-status-badge');
  const bookedList = document.getElementById('ts-booked-list');
  const freeList = document.getElementById('ts-free-list');
  const conflictBanner = document.getElementById('ts-conflict-banner');
  const timelineBar = document.getElementById('ts-timeline-bar');
  const btnSubmit = document.getElementById('btn-submit-booking');

  if (itemBadge) itemBadge.textContent = selectedItemCode || '-';
  if (itemTitle) itemTitle.textContent = `ตารางเวลาการจอง: ${selectedItemCode} (${selectedItemCapacityLabel})`;

  const d = new Date(currentBookingDate);
  const thaiYear = d.getFullYear() + 543;
  const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
  const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;
  if (dateLabel) dateLabel.textContent = `ประจำวันที่ ${formattedDate}`;

  const itemBookings = (currentItemsSchedule && selectedItemId && currentItemsSchedule[selectedItemId]) ? currentItemsSchedule[selectedItemId] : [];

  // Check if current requested range overlaps with any booking on this table
  const isConflict = itemBookings.some(b => {
    return b.start_time < currentBookingEndTime && b.end_time > currentBookingTime;
  });

  if (isConflict) {
    if (statusBadge) {
      statusBadge.innerHTML = `<span class="pill no" style="font-size:12px; padding:5px 12px;">❌ ไม่ว่าง ติดจองช่วง ${currentBookingTime} – ${currentBookingEndTime} น.</span>`;
    }
    if (conflictBanner) {
      conflictBanner.style.display = 'flex';
      const c = itemBookings.find(b => b.start_time < currentBookingEndTime && b.end_time > currentBookingTime);
      const shiftBtn = document.getElementById('btn-quick-shift-time');
      if (shiftBtn && c) {
        shiftBtn.textContent = `สลับเป็น ${c.end_time} น.`;
        shiftBtn.onclick = () => selectQuickSlot(c.end_time);
      }
    }
    if (btnSubmit) {
      btnSubmit.style.background = '#8C8570';
      btnSubmit.style.borderColor = '#8C8570';
      btnSubmit.textContent = `⚠️ โต๊ะนี้ติดจองช่วง ${currentBookingTime}–${currentBookingEndTime} น. (คลิกเพื่อดูเวลาว่าง)`;
    }
  } else {
    if (statusBadge) {
      statusBadge.innerHTML = `<span class="pill ok" style="font-size:12px; padding:5px 12px;">✅ ว่าง พร้อมจองสำหรับช่วงเวลานี้</span>`;
    }
    if (conflictBanner) {
      conflictBanner.style.display = 'none';
    }
    if (btnSubmit) {
      btnSubmit.style.background = '';
      btnSubmit.style.borderColor = '';
      btnSubmit.textContent = `ชำระมัดจำ (฿${currentVenueDeposit.toLocaleString()}) & ยืนยันการจอง`;
    }
  }

  // Populate Booked Intervals
  if (bookedList) {
    if (itemBookings.length === 0) {
      bookedList.innerHTML = `<div style="color:var(--muted); font-size:12px;">ยังไม่มีการจองสำหรับโต๊ะนี้ในวันนี้</div>`;
    } else {
      bookedList.innerHTML = itemBookings.map(b => `
        <div style="display:flex; align-items:center; justify-content:space-between; background:#FFF5EB; border:1px solid #FFDFC7; border-radius:6px; padding:4px 8px; margin-bottom:4px; font-size:11.5px;">
          <span style="font-weight:700; color:var(--rust); display:inline-flex; align-items:center; gap:4px;">
            🔒 ${b.start_time} – ${b.end_time} น.
          </span>
          <span style="color:var(--muted); font-size:11px;">${b.customer_name ? b.customer_name : 'ติดจอง'} (${b.party_size} คน)</span>
        </div>
      `).join('');
    }
  }

  // Populate Free Intervals
  if (freeList) {
    if (itemBookings.length === 0) {
      freeList.innerHTML = `
        <div style="background:#EAF3EB; border:1px solid #C5E2C9; border-radius:6px; padding:4px 8px; font-size:11.5px; color:var(--forest); font-weight:600;">
          🟢 เปิดบริการตลอดวัน (10:00 – 22:00 น.)
        </div>
      `;
    } else {
      const openStart = '10:00';
      const openEnd = '22:00';
      const sortedBookings = [...itemBookings].sort((a, b) => a.start_time.localeCompare(b.start_time));
      let currentMarker = openStart;
      const freeSegments = [];

      sortedBookings.forEach(b => {
        if (b.start_time > currentMarker) {
          freeSegments.push({ start: currentMarker, end: b.start_time });
        }
        if (b.end_time > currentMarker) {
          currentMarker = b.end_time;
        }
      });
      if (currentMarker < openEnd) {
        freeSegments.push({ start: currentMarker, end: openEnd });
      }

      if (freeSegments.length === 0) {
        freeList.innerHTML = `<div style="color:var(--rust); font-size:12px;">เต็มทุกช่วงเวลาแล้ว</div>`;
      } else {
        freeList.innerHTML = freeSegments.map(seg => `
          <div style="display:flex; align-items:center; justify-content:space-between; background:#EAF3EB; border:1px solid #C5E2C9; border-radius:6px; padding:4px 8px; margin-bottom:4px; font-size:11.5px;">
            <span style="font-weight:700; color:var(--forest);">🟢 ${seg.start} – ${seg.end} น.</span>
            <button type="button" class="btn btn-outline btn-xs" style="padding:1px 6px; font-size:10.5px; height:auto;" onclick="selectQuickSlot('${seg.start}')">เลือกเวลานี้</button>
          </div>
        `).join('');
      }
    }
  }

  // Populate Visual Hours Timeline Bar (10:00 to 22:00)
  if (timelineBar) {
    timelineBar.innerHTML = '';
    const timelineHours = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];
    timelineHours.forEach(hStart => {
      const hEnd = minutesToTime(timeToMinutes(hStart) + 60);
      const isBooked = itemBookings.some(b => b.start_time < hEnd && b.end_time > hStart);
      const isSelected = (currentBookingTime < hEnd && currentBookingEndTime > hStart);

      const block = document.createElement('div');
      block.className = 'ts-hour-block';
      const shortH = hStart.split(':')[0];
      block.textContent = `${shortH}h`;

      if (isBooked) {
        block.classList.add('booked');
        block.title = `${hStart} - ${hEnd} น. (ติดจอง)`;
      } else if (isSelected) {
        block.style.background = 'var(--gold)';
        block.style.color = '#1F3327';
        block.title = `${hStart} - ${hEnd} น. (ช่วงที่คุณเลือก)`;
        block.onclick = () => selectQuickSlot(hStart);
      } else {
        block.classList.add('free');
        block.title = `${hStart} - ${hEnd} น. (ว่าง คลิกเพื่อเลือกเวลานี้)`;
        block.onclick = () => selectQuickSlot(hStart);
      }

      timelineBar.appendChild(block);
    });
  }
}

// Slot and Party controls
function selectSlot(el, timeStr) {
  if (el.classList.contains('full')) return;
  document.querySelectorAll('#time-slots .slot').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  currentBookingTime = timeStr;

  const hiddenInput = document.getElementById('search-time');
  if (hiddenInput) hiddenInput.value = timeStr;
  const textEl = document.getElementById('dd-time-text');
  if (textEl) textEl.textContent = `${timeStr} น.`;
  document.querySelectorAll('#timepicker-panel .tp-slot').forEach(s => {
    s.classList.toggle('selected', s.textContent.trim() === timeStr);
  });

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
  const d = new Date(val);
  if (!isNaN(d.getTime())) {
    const textEl = document.getElementById('dd-date-text');
    if (textEl) {
      textEl.textContent = `${d.getDate()} ${thaiMonthShort[d.getMonth()]} ${d.getFullYear() + 543}`;
    }
  }
  const searchDateInput = document.getElementById('search-date');
  if (searchDateInput) searchDateInput.value = val;
  updateDateTimeSummary();
  renderCalendarDays();
  fetchVenueAvailability();
}

function updateBookingDate(val) {
  currentBookingDate = val;
  const d = new Date(val);
  if (!isNaN(d.getTime())) {
    const textEl = document.getElementById('dd-date-text');
    if (textEl) {
      textEl.textContent = `${d.getDate()} ${thaiMonthShort[d.getMonth()]} ${d.getFullYear() + 543}`;
    }
  }
  const bookDateInput = document.getElementById('book-date');
  if (bookDateInput) bookDateInput.value = val;
  updateDateTimeSummary();
  renderCalendarDays();
  fetchVenueAvailability();
}

function updateBookingTime(val) {
  currentBookingTime = val;
  currentBookingEndTime = addHoursToTime(val, currentDurationHours);
  const textEl = document.getElementById('dd-time-text');
  if (textEl) textEl.textContent = `${val} น.`;
  const startSelect = document.getElementById('book-start-time');
  if (startSelect) startSelect.value = val;
  const endSelect = document.getElementById('book-end-time');
  if (endSelect) endSelect.value = currentBookingEndTime;
  document.querySelectorAll('#timepicker-panel .tp-slot').forEach(s => {
    s.classList.toggle('selected', s.textContent.trim() === val);
  });
  document.querySelectorAll('#time-slots .slot').forEach(s => {
    s.classList.toggle('active', s.textContent.trim() === val);
  });
  updateDateTimeSummary();
  fetchVenueAvailability();
}

function updateDateTimeSummary() {
  const d = new Date(currentBookingDate);
  const thaiYear = d.getFullYear() + 543;
  const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
  const formattedDate = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;
  const sumEl = document.getElementById('sum-datetime');
  if (sumEl) {
    sumEl.textContent = `${formattedDate}, ${currentBookingTime} – ${currentBookingEndTime} น.`;
  }
}

// Open Payment Modal before creating booking
function openPaymentModal() {
  const customerName = document.getElementById('book-customer-name').value.trim();
  if (!customerName) {
    alert('กรุณากรอกชื่อผู้จอง');
    return;
  }
  if (!selectedItemId) {
    alert('กรุณาคลิกเลือกโต๊ะหรือสนามที่ต้องการจากผัง');
    return;
  }

  // Prevent booking if item is already booked during this time range
  const itemBookings = (currentItemsSchedule && selectedItemId && currentItemsSchedule[selectedItemId]) ? currentItemsSchedule[selectedItemId] : [];
  const isConflict = itemBookings.some(b => b.start_time < currentBookingEndTime && b.end_time > currentBookingTime);
  if (isConflict) {
    alert(`โต๊ะหรือสนามนี้ติดจองในช่วงเวลา ${currentBookingTime} – ${currentBookingEndTime} น. กรุณาสลับช่วงเวลาหรือเลือกโต๊ะอื่น`);
    return;
  }

  const venue = allVenues.find(v => v.id === currentVenueId);
  const vName = venue ? venue.name : 'สถานที่';
  const deposit = venue && venue.deposit_amount ? parseFloat(venue.deposit_amount) : currentVenueDeposit;
  currentVenueDeposit = deposit;

  // Format date
  const d = new Date(currentBookingDate);
  const thaiYear = d.getFullYear() + 543;
  const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
  const dateStr = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;

  document.getElementById('pay-venue-name').textContent = vName;
  document.getElementById('pay-booking-details').textContent = `${selectedItemCode} (${selectedItemCapacityLabel}) · ${dateStr}, ${currentBookingTime} – ${currentBookingEndTime} น. · ${currentPartySize} ท่าน`;
  document.getElementById('pay-deposit-amount').textContent = `฿${deposit.toLocaleString('th-TH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;

  // Start 15-minute countdown
  startPaymentCountdown(15 * 60);

  // Reset slip preview
  removeSlip();

  document.getElementById('modal-payment').classList.add('active');
}

// Payment Countdown Timer (15 minutes)
function startPaymentCountdown(durationSeconds) {
  if (paymentCountdownInterval) clearInterval(paymentCountdownInterval);
  let remaining = durationSeconds;
  const timerEl = document.getElementById('pay-timer');

  function tick() {
    const mins = Math.floor(remaining / 60);
    const secs = remaining % 60;
    if (timerEl) {
      timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }
    if (remaining <= 0) {
      clearInterval(paymentCountdownInterval);
      if (timerEl) timerEl.textContent = 'หมดเวลา';
    }
    remaining--;
  }
  tick();
  paymentCountdownInterval = setInterval(tick, 1000);
}

// Copy promptpay ref code
function copyRefCode() {
  const refCode = document.getElementById('copy-ref-text').textContent.trim();
  if (navigator.clipboard) {
    navigator.clipboard.writeText(refCode).then(() => {
      showToast('คัดลอกหมายเลขพร้อมเพย์/เลขอ้างอิงแล้ว');
    }).catch(() => {
      showToast('คัดลอกสำเร็จ: ' + refCode);
    });
  } else {
    showToast('หมายเลขพร้อมเพย์: ' + refCode);
  }
}

// Slip upload preview and removal
function handleSlipSelected(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('slip-preview-img').src = e.target.result;
      document.getElementById('slip-filename').textContent = file.name;
      document.getElementById('slip-prompt').style.display = 'none';
      document.getElementById('slip-preview-box').style.display = 'flex';
      showToast('แนบรูปสลิปเรียบร้อยแล้ว');
    };
    reader.readAsDataURL(file);
  }
}

function removeSlip() {
  const input = document.getElementById('slip-input');
  if (input) input.value = '';
  const promptEl = document.getElementById('slip-prompt');
  const previewBox = document.getElementById('slip-preview-box');
  if (promptEl) promptEl.style.display = 'flex';
  if (previewBox) previewBox.style.display = 'none';
}

// Confirm deposit payment and submit booking
async function confirmDepositAndBook() {
  const btn = document.getElementById('btn-confirm-payment');
  const customerName = document.getElementById('book-customer-name').value.trim() || 'สมชาย ขยันงาน';
  const customerPhone = document.getElementById('book-customer-phone').value.trim() || '081-234-5678';

  btn.disabled = true;
  btn.textContent = 'กำลังยืนยันการชำระเงิน...';

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
        booking_end_time: currentBookingEndTime,
        party_size: currentPartySize,
        customer_name: customerName,
        customer_phone: customerPhone,
        deposit_amount: currentVenueDeposit,
      })
    });

    if (response.status === 422) {
      const errData = await response.json();
      alert(errData.message || 'โต๊ะนี้ติดจองในช่วงเวลาดังกล่าว กรุณาเลือกช่วงเวลาอื่น');
      fetchVenueAvailability();
      return;
    }

    const data = await response.json();

    if (data.success && data.booking) {
      const b = data.booking;
      closeModal('modal-payment');
      if (paymentCountdownInterval) clearInterval(paymentCountdownInterval);

      const timeRangeText = `${b.booking_time} – ${b.booking_end_time || currentBookingEndTime} น.`;

      // Populate Ticket View
      document.getElementById('ticket-code').textContent = b.booking_code;
      document.getElementById('ticket-venue').textContent = b.venue ? b.venue.name : 'ร้านอาหาร';
      document.getElementById('ticket-item').textContent = `${selectedItemCode} · ${selectedItemCapacityLabel}`;
      
      const d = new Date(b.booking_date);
      const thaiYear = d.getFullYear() + 543;
      const monthNames = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
      const dateText = `${d.getDate()} ${monthNames[d.getMonth()]} ${thaiYear}`;
      
      document.getElementById('ticket-date').textContent = dateText;
      document.getElementById('ticket-time').textContent = timeRangeText;
      document.getElementById('ticket-party').textContent = `${b.party_size} คน`;
      document.getElementById('ticket-name').textContent = b.customer_name;
      const depositEl = document.getElementById('ticket-deposit');
      if (depositEl) {
        depositEl.textContent = `฿${parseFloat(b.deposit_amount).toLocaleString()} (ชำระมัดจำแล้วผ่าน PromptPay QR)`;
      }

      // Prepend to My Bookings Container
      prependMyBookingCard(b, dateText, timeRangeText);

      // Prepend to Owner Dashboard Table
      prependOwnerRow(b);

      // Re-fetch availability to update floorplan and schedule immediately
      fetchVenueAvailability();

      showToast(`ชำระเงินมัดจำ ฿${parseFloat(b.deposit_amount).toLocaleString()} สำเร็จ! ได้รับการยืนยันทันที`);
      showScreen('confirm', null);
    } else {
      alert('เกิดข้อผิดพลาดในการบันทึกการจอง กรุณาลองใหม่อีกครั้ง');
    }
  } catch (err) {
    console.error(err);
    alert('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้');
  } finally {
    btn.disabled = false;
    btn.textContent = 'ยืนยันการชำระเงินและรับใบจอง';
  }
}

// Fallback alias for submitBooking
function submitBooking() {
  openPaymentModal();
}

function prependMyBookingCard(b, dateText, timeRangeText) {
  const container = document.getElementById('my-bookings-container');
  if (!container) return;
  const card = document.createElement('div');
  card.className = 'bcard my-booking-card';
  card.setAttribute('data-tab', 'upcoming');
  const gradient = (b.venue && b.venue.gradient_thumb) ? b.venue.gradient_thumb : 'linear-gradient(135deg,#C1642F,#8C3B24)';
  const depAmount = parseFloat(b.deposit_amount) || 0;
  const displayTime = timeRangeText || `${b.booking_time} – ${b.booking_end_time || currentBookingEndTime} น.`;
  
  card.innerHTML = `
    <div class="thumb" style="background:${gradient};"></div>
    <div class="info">
      <div class="name">${b.venue ? b.venue.name : 'ร้านอาหาร'} · ${selectedItemCode}</div>
      <div class="meta">${dateText}, ${displayTime} · ${b.party_size} คน</div>
      <div class="code mono">${b.booking_code}</div>
    </div>
    <span class="pill ok">ยืนยันแล้ว</span>
    ${depAmount > 0 ? `<span class="pill" style="background:#FAF3E3; color:#9B6C1E; font-size:11px; font-weight:600;">มัดจำ ฿${depAmount.toLocaleString()}</span>` : ''}
    <button class="btn btn-ghost btn-sm" onclick="viewTicketDetails({
      code: '${b.booking_code}',
      venue: '${b.venue ? b.venue.name : ''}',
      item: '${selectedItemCode} · ${selectedItemCapacityLabel}',
      date: '${dateText}',
      time: '${displayTime}',
      party: '${b.party_size} คน',
      name: '${b.customer_name}',
      deposit: '฿${depAmount.toLocaleString()} (ชำระแล้วผ่าน PromptPay QR)'
    })">ดูรายละเอียด</button>
  `;
  container.prepend(card);
}

function prependOwnerRow(b) {
  const tbody = document.getElementById('owner-bookings-tbody');
  if (!tbody) return;
  const tr = document.createElement('tr');
  tr.id = `owner-row-${b.id}`;
  const depAmount = parseFloat(b.deposit_amount) || 0;
  const displayTime = `${b.booking_time} – ${b.booking_end_time || currentBookingEndTime}`;
  tr.innerHTML = `
    <td class="mono"><b>${displayTime}</b></td>
    <td>${b.customer_name}</td>
    <td>${selectedItemCode}</td>
    <td>${b.party_size}</td>
    <td><b class="mono" style="color:var(--forest);">฿${depAmount.toLocaleString()}</b></td>
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
  const depositEl = document.getElementById('ticket-deposit');
  if (depositEl) {
    depositEl.textContent = info.deposit || '฿250 (ชำระมัดจำแล้วผ่าน PromptPay QR)';
  }
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
