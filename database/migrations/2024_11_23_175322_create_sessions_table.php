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
            $table->foreignId('course_detail_id')->constrained('course_details')->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->enum('attendance',['online','offline'])->default('offline');
            $table->enum('day',['saturday','sunday','monday','tuesday','wednesday','thursday']);
            $table->time('from');
            $table->time('to');
            $table->enum('status',['in time','started','finished','postponed'])->default('in time');
            $table->tinyInteger('week')->default(1);
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
