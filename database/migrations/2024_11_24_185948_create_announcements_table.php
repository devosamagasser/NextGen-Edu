<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('departments')->unllOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->unllOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->unllOnDelete();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('cover')->nullable();
            $table->timestamp('time_to_post')->useCurrent();
            $table->time('time')->default(DB::raw('CURRENT_TIME'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
