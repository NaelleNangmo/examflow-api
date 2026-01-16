<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('semester_result_id')->nullable();
            $table->enum('transcript_type', ['PROVISIONAL', 'FINAL', 'MAKEUP']);
            $table->string('transcript_number')->unique();
            $table->date('issue_date');
            $table->uuid('issued_by');
            $table->string('signature_name')->nullable();
            $table->string('signature_title')->nullable();
            $table->boolean('is_official')->default(false);
            $table->string('qr_code')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('semester_result_id')->references('id')->on('semester_results')->onDelete('set null');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
