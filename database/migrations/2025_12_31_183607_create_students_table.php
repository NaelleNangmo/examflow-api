<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->enum('student_status', ['REGULAR', 'REPEATING', 'WITH_DEBT', 'GRADUATED', 'DROPPED_OUT'])->default('REGULAR');
            $table->enum('regime', ['FULL_TIME', 'PART_TIME'])->default('FULL_TIME');
            $table->uuid('program_id');
            $table->uuid('level_id');
            $table->string('promotion', 10); // ex: "2024"
            $table->date('enrollment_date');
            $table->date('expected_graduation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
