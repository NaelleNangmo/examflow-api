<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10); // L1, L2, L3, M1, M2
            $table->string('name');
            $table->uuid('program_id');
            $table->integer('year_number'); // 1, 2, 3, etc.
            $table->integer('credits');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->unique(['code', 'program_id'], 'levels_code_program_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
