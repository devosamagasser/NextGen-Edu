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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('course_detail_id')->nullable()->constrained('course_details')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->tinyInteger('total_degree');
            $table->date('date'); 
            $table->time('start_time'); 
            $table->integer('duration');
            $table->enum('status', ['finished', 'started', 'scheduled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
