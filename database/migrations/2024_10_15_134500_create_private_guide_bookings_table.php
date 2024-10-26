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
        Schema::create('private_guide_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guide_id')->constrained('guides')->onDelete('cascade');

            $table->date('bookingDate');
            $table->time('startDate');
            $table->time('endDate');
            $table->decimal('totalCost', 10, 2);
            $table->enum('bookingStatus', ['pending', 'confirmed', 'canceled'])->default('pending');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_guide_bookings');
    }
};
