<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique(); // ex: "S1-2024-2025"
            $table->string('name');
            $table->uuid('academic_year_id');
            $table->integer('semester_number'); // 1 ou 2
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->date('exam_session_start')->nullable();
            $table->date('exam_session_end')->nullable();
            $table->date('makeup_session_start')->nullable();
            $table->date('makeup_session_end')->nullable();
            $table->enum('status', ['ACTIVE', 'CLOSED', 'PLANNED'])->default('PLANNED');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
