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
        Schema::create('postponed_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->cascadeOnDelete();
            $table->foreignId('hall_id')->nullable()->constrained('halls')->cascadeOnDelete();
            $table->enum('attendance',['online','offline'])->default('offline');
            $table->date('day');
            $table->time('from');
            $table->time('to');
            $table->enum('status',['started','finished']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postponed_sessions');
    }
};
