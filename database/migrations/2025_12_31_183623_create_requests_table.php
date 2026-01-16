<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_number')->unique();
            $table->enum('type', ['GRADE_CONTESTATION', 'COPY_REVISION', 'FORCE_MAJEURE', 'DEROGATION', 'ADMINISTRATIVE_CLAIM', 'TRANSCRIPT_DUPLICATE', 'ATTESTATION']);
            $table->string('subject');
            $table->text('description');
            $table->uuid('student_id');
            $table->uuid('course_unit_id')->nullable();
            $table->uuid('grade_id')->nullable();
            $table->json('attachments')->nullable(); // URLs des fichiers
            $table->enum('status', ['PENDING', 'IN_REVIEW', 'APPROVED', 'REJECTED', 'RESOLVED', 'CANCELLED'])->default('PENDING');
            $table->uuid('assigned_to')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('response_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('set null');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
