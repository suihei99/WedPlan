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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('task_name'); // Task name
            $table->text('description')->nullable(); // Task description
            $table->date('deadline')->nullable(); // Task due date
            $table->boolean('is_completed')->default(false); // Task completion status
            $table->tinyInteger('priority')->default(0); // Task priority (e.g., 0 = low, 1 = medium, 2 = high)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
