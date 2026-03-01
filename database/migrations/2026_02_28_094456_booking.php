<?php

use App\Models\User;
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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade'); // Foreign key to users table for couple booking
            $table->string('type_service'); // Type of service being booked (e.g., Venue, Catering, etc.)
            $table->date('booking_date');
            $table->boolean('status')->default(true); // booking status (e.g., , confirmed = true, canceled = false)
            $table->string('notes')->nullable(); // Additional notes or special requests
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
