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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('name'); // Guest name
            $table->integer('pax_count')->nullable(); // Number of people in the guest's party 
            $table->string('phone')->nullable(); // Guest phone number
            $table->string('rsvp_status')->default('pending'); // RSVP status (e.g., pending, accepted, declined)
            $table->text('qr_code_string')->nullable(); // QR code string for the guest
            $table->string('invite_code')->nullable(); // Unique invite code for the guest
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
