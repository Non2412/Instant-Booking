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
        Schema::create('venue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('item_code'); // e.g. T1, T2, คอร์ท 1, Room A
            $table->string('name')->nullable();
            $table->string('capacity_label'); // e.g. 4 ที่นั่ง, 1 ชม.
            $table->unsignedSmallInteger('capacity')->default(4);
            $table->string('shape')->default('round'); // round, square
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_items');
    }
};
