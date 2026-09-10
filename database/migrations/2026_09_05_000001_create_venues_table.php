<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // restaurant, sports, cafe, meeting_room
            $table->string('category_badge'); // e.g. ร้านอาหาร, สนามแบดมินตัน, คาเฟ่
            $table->string('category_subtitle'); // e.g. อาหารไทย-อีสาน
            $table->string('province')->default('ชัยภูมิ');
            $table->string('address')->nullable();
            $table->string('price_level')->nullable(); // ฿฿
            $table->decimal('rating', 2, 1)->default(4.5);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('opening_hours')->default('10:00–22:00');
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->string('gradient_thumb')->nullable();
            $table->text('image_url')->nullable();
            $table->decimal('deposit_amount', 10, 2)->default(250.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
