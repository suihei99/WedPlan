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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Common fields for all users (couples, vendors, and admins)
            $table->string('email')->unique(); // Email column for user authentication
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // Password column for authentication
            $table->string('role')->default('couple'); // Role column to distinguish between admin , couple and vendor
            $table->string('device_token')->nullable(); // Token for mobile app authentication
            $table->text('profile_photo_path')->nullable(); // Profile photo path for users (optional)

            // For couples, we can store their wedding details in the Couple table
            // For vendors, we can store the business name and contact information in the Vendor table
            // For admin, we can just use the email and password for authentication

            // Additional fields for vendors and couples can be added in their respective tables (e.g., couples table for wedding details, vendors table for business information)
            $table->boolean('is_active')->default(true); // Active status for users (e.g., active, inactive)
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
