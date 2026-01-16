<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_unit_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('course_unit_id');
            $table->uuid('semester_id');
            $table->date('enrollment_date');
            $table->enum('status', ['ENROLLED', 'DROPPED', 'COMPLETED'])->default('ENROLLED');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            
            $table->unique(['student_id', 'course_unit_id', 'semester_id'], 'cu_enrollments_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_unit_enrollments');
    }
};
