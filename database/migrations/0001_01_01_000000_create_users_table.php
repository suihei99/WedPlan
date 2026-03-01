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
            $table->string('email')->unique(); // Email column for user authentication
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // Password column for authentication
            $table->string('role'); // Role column to distinguish between admin , couple and vendor
            $table->string('device_token')->nullable(); // Token for mobile app authentication

            // For couples, we can store the names of both partners in the same row
            $table->string('partner_1_name')->nullable(); // Partner 1 name
            $table->string('partner_2_name')->nullable(); // Partner 2 name
            $table->date('wedding_date')->nullable(); // Wedding date for couples
            $table->time('wedding_time')->nullable(); // Wedding time for couples
            $table->string('wedding_venue')->nullable(); // Wedding venue for couples
            $table->decimal('total_budget_limit', 10, 2)->nullable(); // Budget for couples

            // For vendors, we can store the business name and contact information
            $table->string('business_name')->nullable(); // Business name for vendors
            $table->string('business_type')->nullable(); // Service type for vendors (e.g., catering, photography)
            $table->string('contact_number')->nullable(); // Contact number for vendors
            $table->string('status')->default('pending'); // Status for vendors (e.g., pending, approved, rejected)
            $table->text('address')->nullable(); // Address for vendors
            $table->text('business_documents')->nullable(); // Business documents for vendors (e.g., licenses, certifications)


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
