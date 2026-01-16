<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->enum('grade', ['PROFESSOR', 'ASSOCIATE_PROFESSOR', 'LECTURER', 'ASSISTANT', 'TUTOR'])->default('LECTURER');
            $table->string('specialty');
            $table->enum('teacher_type', ['FULL_TIME', 'PART_TIME', 'VISITING'])->default('FULL_TIME');
            $table->uuid('department_id');
            $table->date('hire_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
