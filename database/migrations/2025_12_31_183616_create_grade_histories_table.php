<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grade_id');
            $table->enum('action', ['CREATED', 'UPDATED', 'SUBMITTED', 'VALIDATED', 'REJECTED', 'FINALIZED']);
            $table->uuid('performed_by');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('performed_at');
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_histories');
    }
};
