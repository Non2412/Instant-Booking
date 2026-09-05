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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // e.g. RSV-2609-7741
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->date('booking_date');
            $table->string('booking_time'); // e.g. 19:00
            $table->unsignedSmallInteger('party_size')->default(1);
            $table->string('status')->default('confirmed'); // confirmed, pending_deposit, completed, cancelled
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->text('special_request')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
