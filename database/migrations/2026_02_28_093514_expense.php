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
        Schema::create('expenses', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('expense_name'); // Name of the expense
            $table->decimal('amount', 10, 2); // Amount of the expense
            $table->date('date_paid')->nullable(); // Date when the expense was paid
            $table->text('description')->nullable(); // Additional notes about the expense
            $table->string('paymenbt_method')->nullable(); // Payment method (e.g., credit card, cash, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
