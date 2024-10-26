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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('AreaName');
            $table->integer('NumberOfPeople');
            $table->integer('Confirmed')->default(0);
            $table->integer('Pending')->default(0);
            $table->decimal('Cost', 10, 2);
            $table->text('TripDetails');
            $table->text('TripHistory');
            $table->dateTime('RegistrationStartDate');
            $table->dateTime('RegistrationEndDate');
            $table->boolean('Completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
