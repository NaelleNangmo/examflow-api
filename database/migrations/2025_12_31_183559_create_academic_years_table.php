<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique(); // ex: "2024-2025"
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['ACTIVE', 'CLOSED', 'PLANNED'])->default('PLANNED');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
