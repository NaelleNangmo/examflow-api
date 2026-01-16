<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('teacher_id');
            $table->uuid('course_unit_id');
            $table->enum('assignment_type', ['RESPONSIBLE', 'CO_TEACHER'])->default('RESPONSIBLE');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('cascade');
            
            $table->unique(['teacher_id', 'course_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
