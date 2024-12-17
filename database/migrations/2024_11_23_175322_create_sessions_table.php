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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['lecture','section','lab']);
            $table->foreignId('course_detail_id')->constrained('course_details');
            $table->foreignId('hall_id')->constrained('halls');
            $table->enum('attendance',['online','offline'])->default('offline');
            $table->date('day');
            $table->time('from');
            $table->time('to');
            $table->enum('status',['started','finished','postponed']);
            $table->tinyInteger('week');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
