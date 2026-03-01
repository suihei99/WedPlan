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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('service_name'); // Name of the service (e.g., Catering, Photography, etc.)
            $table->string('type_service')->nullable(); // Type of service (e.g., Food, Entertainment, etc.)
            $table->decimal('price_estimate', 10, 2); // Estimated price of the service
            $table->text('description')->nullable(); // Additional notes about the service
            $table->text('image_url')->nullable(); // URL of the service image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
