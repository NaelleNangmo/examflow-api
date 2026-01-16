<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grade_id')->nullable();
            $table->uuid('student_id');
            $table->uuid('course_unit_id');
            $table->uuid('semester_id');
            $table->enum('action_type', ['CREATE', 'UPDATE', 'VALIDATE_PEDAGOGICAL', 'VALIDATE_ADMINISTRATIVE', 'REJECT'])->default('CREATE');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->uuid('actor_id');
            $table->string('actor_role');
            $table->text('comment')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->enum('validation_level', ['NONE', 'PEDAGOGICAL', 'ADMINISTRATIVE'])->default('NONE');
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['grade_id', 'action_type']);
            $table->index(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_audits');
    }
};
