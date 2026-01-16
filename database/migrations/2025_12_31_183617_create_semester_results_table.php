<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('semester_id');
            $table->uuid('academic_year_id');
            $table->uuid('program_id');
            $table->uuid('level_id');
            $table->integer('total_credits_enrolled');
            $table->integer('total_credits_acquired');
            $table->decimal('gpa', 5, 2); // Moyenne générale
            $table->integer('rank');
            $table->integer('total_students');
            $table->enum('mention', ['PASSABLE', 'ASSEZ_BIEN', 'BIEN', 'TRES_BIEN', 'EXCELLENT'])->nullable();
            $table->enum('decision', ['ADMITTED', 'ADMITTED_WITH_COMPENSATION', 'ADMITTED_WITH_CONDITION', 'DEFERRED', 'REPEAT_YEAR', 'EXPELLED']);
            $table->text('decision_comment')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->uuid('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_results');
    }
};
