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
        Schema::table('course_details', function (Blueprint $table) {
            $table->unique(['course_id', 'department_id', 'semester_id', 'teacher_id'], 'unique_course_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_details', function (Blueprint $table) {
            $table->dropUnique(['course_id', 'department_id', 'semester_id', 'teacher_id']);
        });
    }
};
