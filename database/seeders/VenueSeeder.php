<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ครัวคุณยาย (ร้านอาหาร)
        $kruaYai = Venue::create([
            'name' => 'ครัวคุณยาย',
            'type' => 'restaurant',
            'category_badge' => 'ร้านอาหาร',
            'category_subtitle' => 'อาหารไทย-อีสาน · ชัยภูมิ · ฿฿',
            'province' => 'ชัยภูมิ',
            'address' => 'อ.เมือง, ชัยภูมิ',
            'price_level' => '฿฿',
            'rating' => 4.7,
            'reviews_count' => 312,
            'opening_hours' => 'เปิด 11:00–21:30',
            'description' => 'ร้านอาหารไทย-อีสานบรรยากาศบ้านสวน เสิร์ฟเมนูซิกเนเจอร์อย่างส้มตำและไก่ย่างสูตรโบราณ รองรับกลุ่มครอบครัวและงานเลี้ยงขนาดเล็ก มีที่จอดรถและโซนกลางแจ้ง',
            'amenities' => ['ที่จอดรถฟรี', 'รองรับเด็ก', 'Wi-Fi', 'ห้องปรับอากาศ', 'จ่ายผ่านแอปได้'],
            'gradient_thumb' => 'linear-gradient(135deg,#C1642F,#8C3B24)',
        ]);

        $tables = [
            ['item_code' => 'T1', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 1],
            ['item_code' => 'T2', 'capacity_label' => '2 ที่นั่ง', 'capacity' => 2, 'shape' => 'round', 'sort_order' => 2],
            ['item_code' => 'T3', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 3],
            ['item_code' => 'T4', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 4],
            ['item_code' => 'T5', 'capacity_label' => '6 ที่นั่ง', 'capacity' => 6, 'shape' => 'round', 'sort_order' => 5],
            ['item_code' => 'T6', 'capacity_label' => '2 ที่นั่ง', 'capacity' => 2, 'shape' => 'round', 'sort_order' => 6],
            ['item_code' => 'T7', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 7],
            ['item_code' => 'T8', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 8],
            ['item_code' => 'T9', 'capacity_label' => '8 ที่นั่ง', 'capacity' => 8, 'shape' => 'round', 'sort_order' => 9],
            ['item_code' => 'T10', 'capacity_label' => '4 ที่นั่ง', 'capacity' => 4, 'shape' => 'round', 'sort_order' => 10],
            ['item_code' => 'T11', 'capacity_label' => '6 ที่นั่ง', 'capacity' => 6, 'shape' => 'round', 'sort_order' => 11],
            ['item_code' => 'T12', 'capacity_label' => '2 ที่นั่ง', 'capacity' => 2, 'shape' => 'round', 'sort_order' => 12],
        ];

        $kruaItems = [];
        foreach ($tables as $tableData) {
            $kruaItems[$tableData['item_code']] = $kruaYai->items()->create($tableData);
        }

        // 2. อารีน่า สปอร์ตคลับ (สนามแบดมินตัน)
        $arenaSports = Venue::create([
            'name' => 'อารีน่า สปอร์ตคลับ',
            'type' => 'sports',
            'category_badge' => 'สนามแบดมินตัน',
            'category_subtitle' => 'สนามในร่ม 8 คอร์ท · เปิด 06:00–22:00',
            'province' => 'ชัยภูมิ',
            'address' => 'อ.เมือง, ชัยภูมิ',
            'price_level' => '฿',
            'rating' => 4.9,
            'reviews_count' => 188,
            'opening_hours' => 'เปิด 06:00–22:00',
            'description' => 'สนามแบดมินตันมาตรฐานแข่งขัน 8 คอร์ท พื้นสังเคราะห์กันลื่น แอร์เย็นทั่วถึง มีร้านเช่า-ขายอุปกรณ์ และจุดจอดรถกว้างขวาง',
            'amenities' => ['สนามในร่ม', 'ที่จอดรถกว้าง', 'เช่าอุปกรณ์', 'ห้องอาบน้ำ', 'ติดแอร์'],
            'gradient_thumb' => 'linear-gradient(135deg,#2F6480,#1D4256)',
        ]);

        $arenaItems = [];
        for ($i = 1; $i <= 8; $i++) {
            $arenaItems['คอร์ท '.$i] = $arenaSports->items()->create([
                'item_code' => 'คอร์ท '.$i,
                'name' => 'คอร์ท '.$i,
                'capacity_label' => '1 ชม.',
                'capacity' => 4,
                'shape' => 'square',
                'sort_order' => $i,
            ]);
        }

        // 3. กรีนลีฟ คาเฟ่
        $greenLeaf = Venue::create([
            'name' => 'กรีนลีฟ คาเฟ่',
            'type' => 'cafe',
            'category_badge' => 'คาเฟ่',
            'category_subtitle' => 'คาเฟ่บรรยากาศสวน · ชัยภูมิ · ฿฿',
            'province' => 'ชัยภูมิ',
            'address' => 'ถ.สนามบิน, ชัยภูมิ',
            'price_level' => '฿฿',
            'rating' => 4.5,
            'reviews_count' => 96,
            'opening_hours' => 'เปิด 08:30–18:00',
            'description' => 'คาเฟ่ในสวนร่มรื่น กาแฟ Specialty Coffee ขนมโฮมเมด และอาหารจานเดียว บรรยากาศเงียบสงบเหมาะแก่การพักผ่อนหรือนั่งทำงาน',
            'amenities' => ['ที่จอดรถฟรี', 'Wi-Fi', 'โซนห้องแอร์', 'โซนเอาท์ดอร์', 'ปลั๊กไฟ'],
            'gradient_thumb' => 'linear-gradient(135deg,#3F7A52,#255238)',
        ]);

        $greenItems = [];
        for ($i = 1; $i <= 12; $i++) {
            $greenItems['โต๊ะ '.$i] = $greenLeaf->items()->create([
                'item_code' => 'โต๊ะ '.$i,
                'name' => 'โต๊ะ '.$i,
                'capacity_label' => ($i % 3 === 0 ? '4 ที่นั่ง' : '2 ที่นั่ง'),
                'capacity' => ($i % 3 === 0 ? 4 : 2),
                'shape' => 'round',
                'sort_order' => $i,
            ]);
        }

        // 4. ชัยภูมิ ฟุตบอล อารีน่า
        $footballArena = Venue::create([
            'name' => 'ชัยภูมิ ฟุตบอล อารีน่า',
            'type' => 'sports',
            'category_badge' => 'สนามฟุตบอล',
            'category_subtitle' => 'หญ้าเทียม 5 คอร์ท · ไฟสปอตไลต์',
            'province' => 'ชัยภูมิ',
            'address' => 'ต.ในเมือง, ชัยภูมิ',
            'price_level' => '฿฿',
            'rating' => 4.6,
            'reviews_count' => 240,
            'opening_hours' => 'เปิด 15:00–24:00',
            'description' => 'สนามฟุตบอลหญ้าเทียมคุณภาพสูง 7 คน และ 9 คน สปอร์ตไลต์สว่างมาตรฐาน มีคลับเฮาส์และน้ำดื่มบริการ',
            'amenities' => ['หญ้าเทียมเกรดพรีเมียม', 'สปอตไลต์', 'ห้องอาบน้ำ', 'ที่จอดรถ', 'ห้องพักนักกีฬา'],
            'gradient_thumb' => 'linear-gradient(135deg,#C0923A,#8C6620)',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $footballArena->items()->create([
                'item_code' => 'สนาม '.$i,
                'name' => 'สนาม '.$i,
                'capacity_label' => '1 ชม.',
                'capacity' => 14,
                'shape' => 'square',
                'sort_order' => $i,
            ]);
        }

        // 5. บ้านสวนปิ้งย่าง
        $grillGarden = Venue::create([
            'name' => 'บ้านสวนปิ้งย่าง',
            'type' => 'restaurant',
            'category_badge' => 'ร้านอาหาร',
            'category_subtitle' => 'ปิ้งย่างบุฟเฟต์ · ที่จอดรถกว้าง',
            'province' => 'ชัยภูมิ',
            'address' => 'บายพาสชัยภูมิ',
            'price_level' => '฿฿',
            'rating' => 4.4,
            'reviews_count' => 410,
            'opening_hours' => 'เปิด 16:00–23:00',
            'description' => 'บุฟเฟต์หมูกระทะและเนื้อย่างกระทะร้อน วัตถุดิบสดใหม่ น้ำจิ้มรสเด็ด พร้อมไอศกรีมและของหวานไม่อั้น',
            'amenities' => ['บุฟเฟต์ไม่จำกัดเวลา', 'ที่จอดรถ 50 คัน', 'ดนตรีสด', 'เครื่องดื่มรีฟิล'],
            'gradient_thumb' => 'linear-gradient(135deg,#A2483C,#6B2E25)',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            $grillGarden->items()->create([
                'item_code' => 'B'.$i,
                'name' => 'โต๊ะ B'.$i,
                'capacity_label' => '4 ที่นั่ง',
                'capacity' => 4,
                'shape' => 'square',
                'sort_order' => $i,
            ]);
        }

        // 6. โคเวิร์ก สเปซ ชัยภูมิ
        $cowork = Venue::create([
            'name' => 'โคเวิร์ก สเปซ ชัยภูมิ',
            'type' => 'meeting_room',
            'category_badge' => 'ห้องประชุม',
            'category_subtitle' => 'ห้องประชุม 6-20 ที่นั่ง · จอโปรเจกเตอร์',
            'province' => 'ชัยภูมิ',
            'address' => 'ถ.หักคอช้าง, ชัยภูมิ',
            'price_level' => '฿฿',
            'rating' => 4.8,
            'reviews_count' => 54,
            'opening_hours' => 'เปิด 09:00–21:00',
            'description' => 'พื้นที่ทำงานและห้องประชุมส่วนตัว พร้อมสิ่งอำนวยความสะดวกครบครัน จอสมาร์ททีวี ไมค์ไร้สาย และบริการกาแฟสด',
            'amenities' => ['จอโปรเจกเตอร์/TV', 'Wi-Fi ความเร็วสูง', 'เครื่องปรับอากาศ', 'กระดานไวท์บอร์ด', 'บริการเครื่องดื่ม'],
            'gradient_thumb' => 'linear-gradient(135deg,#2B4638,#16241C)',
        ]);

        for ($i = 1; $i <= 4; $i++) {
            $cowork->items()->create([
                'item_code' => 'Room '.chr(64 + $i),
                'name' => 'ห้องประชุม '.chr(64 + $i),
                'capacity_label' => ($i === 1 ? '6 ที่นั่ง' : ($i === 2 ? '10 ที่นั่ง' : '20 ที่นั่ง')),
                'capacity' => ($i === 1 ? 6 : ($i === 2 ? 10 : 20)),
                'shape' => 'square',
                'sort_order' => $i,
            ]);
        }

        // สร้าง Initial Bookings สำหรับ วันนี้ (Owner dashboard)
        $today = Carbon::today();

        Booking::create([
            'booking_code' => 'RSV-2609-2210',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T2']->id ?? null,
            'customer_name' => 'คุณวิภา ร.',
            'customer_phone' => '082-345-6789',
            'customer_email' => 'wipha@example.com',
            'booking_date' => $today,
            'booking_time' => '18:00',
            'party_size' => 2,
            'status' => 'confirmed',
            'deposit_amount' => 0,
        ]);

        Booking::create([
            'booking_code' => 'RSV-2609-7741',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T5']->id ?? null,
            'customer_name' => 'สมชาย ขยันงาน',
            'customer_phone' => '081-234-5678',
            'customer_email' => 'somchai@example.com',
            'booking_date' => $today,
            'booking_time' => '19:00',
            'party_size' => 4,
            'status' => 'confirmed',
            'deposit_amount' => 0,
        ]);

        Booking::create([
            'booking_code' => 'RSV-2609-3345',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T9']->id ?? null,
            'customer_name' => 'คุณธนกร ม.',
            'customer_phone' => '083-456-7890',
            'customer_email' => 'thanakorn@example.com',
            'booking_date' => $today,
            'booking_time' => '19:30',
            'party_size' => 8,
            'status' => 'pending_deposit',
            'deposit_amount' => 200,
        ]);

        Booking::create([
            'booking_code' => 'RSV-2609-4456',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T4']->id ?? null,
            'customer_name' => 'คุณอรุณี ใ.',
            'customer_phone' => '084-567-8901',
            'customer_email' => 'arunee@example.com',
            'booking_date' => $today,
            'booking_time' => '20:30',
            'party_size' => 3,
            'status' => 'pending_deposit',
            'deposit_amount' => 200,
        ]);

        // โต๊ะที่ติดจองก่อนหน้านี้ในผัง T3, T7, T11
        Booking::create([
            'booking_code' => 'RSV-2609-1003',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T3']->id ?? null,
            'customer_name' => 'คุณกิตติศักดิ์',
            'customer_phone' => '085-111-2222',
            'booking_date' => $today,
            'booking_time' => '19:00',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);

        Booking::create([
            'booking_code' => 'RSV-2609-1007',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T7']->id ?? null,
            'customer_name' => 'คุณชัชวาลย์',
            'customer_phone' => '086-333-4444',
            'booking_date' => $today,
            'booking_time' => '19:00',
            'party_size' => 4,
            'status' => 'confirmed',
        ]);

        Booking::create([
            'booking_code' => 'RSV-2609-1011',
            'venue_id' => $kruaYai->id,
            'venue_item_id' => $kruaItems['T11']->id ?? null,
            'customer_name' => 'คุณศิริพร',
            'customer_phone' => '087-555-6666',
            'booking_date' => $today,
            'booking_time' => '19:00',
            'party_size' => 6,
            'status' => 'confirmed',
        ]);

        // Bookings สำหรับผู้ใช้จำลอง (สมชาย ขยันงาน) สำหรับแท็บ My Bookings
        Booking::create([
            'booking_code' => 'RSV-2609-8830',
            'venue_id' => $arenaSports->id,
            'venue_item_id' => $arenaItems['คอร์ท 3']->id ?? null,
            'customer_name' => 'สมชาย ขยันงาน',
            'customer_phone' => '081-234-5678',
            'customer_email' => 'somchai@example.com',
            'booking_date' => $today->copy()->addDays(3),
            'booking_time' => '20:00',
            'party_size' => 4,
            'status' => 'pending_deposit',
            'deposit_amount' => 150,
        ]);

        Booking::create([
            'booking_code' => 'RSV-2608-1190',
            'venue_id' => $greenLeaf->id,
            'venue_item_id' => $greenItems['โต๊ะ 12']->id ?? null,
            'customer_name' => 'สมชาย ขยันงาน',
            'customer_phone' => '081-234-5678',
            'customer_email' => 'somchai@example.com',
            'booking_date' => $today->copy()->subDays(8),
            'booking_time' => '10:00',
            'party_size' => 2,
            'status' => 'completed',
            'deposit_amount' => 0,
        ]);
    }
}
