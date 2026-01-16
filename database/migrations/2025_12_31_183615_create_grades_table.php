<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('course_unit_id');
            $table->uuid('semester_id');
            $table->enum('session_type', ['NORMAL', 'MAKEUP'])->default('NORMAL');
            $table->decimal('grade_cc', 5, 2)->nullable(); // Note contrôle continu (0-20)
            $table->decimal('grade_exam', 5, 2)->nullable(); // Note examen (0-20)
            $table->decimal('grade_final', 5, 2)->nullable(); // Note finale calculée
            $table->boolean('is_absent')->default(false);
            $table->boolean('absence_justified')->nullable();
            $table->boolean('is_fraud')->default(false);
            $table->text('fraud_description')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'PRE_VALIDATED', 'VALIDATED', 'REJECTED', 'FINAL'])->default('DRAFT');
            $table->uuid('entered_by'); // ID de l'enseignant
            $table->timestamp('entered_at')->nullable();
            $table->uuid('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('entered_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['student_id', 'course_unit_id', 'semester_id', 'session_type'], 'grades_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
