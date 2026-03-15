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
        Schema::create('couples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('partner_1_name'); // Partner 1 name
            $table->string('partner_2_name'); // Partner 2 name
            $table->date('wedding_date'); // Wedding date for couples
            $table->time('wedding_time'); // Wedding time for couples
            $table->string('wedding_venue'); // Wedding venue for couples
            $table->decimal('total_budget_limit', 10, 2); // Budget for couples
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couples');
    }
};
