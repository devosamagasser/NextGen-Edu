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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->foreignId('course_detail_id')->nullable()->constrained('course_details')->cascadeOnDelete();
            $table->string('material');
            $table->enum('type',['lecture','section','other']);
            $table->enum('week', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
