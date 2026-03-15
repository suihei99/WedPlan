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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('business_name'); // Business name for vendors
            $table->string('business_type'); // Service type for vendors (e.g., catering, photography)
            $table->string('contact_number'); // Contact number for vendors
            $table->string('status')->default('pending'); // Status for vendors (e.g., pending, approved, rejected)
            $table->text('address')->nullable(); // Address for vendors
            $table->text('business_documents')->nullable(); // Business documents for vendors (e.g., licenses, certifications)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
