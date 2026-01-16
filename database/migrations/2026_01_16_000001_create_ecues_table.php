<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('course_unit_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('coefficient')->default(1);
            $table->integer('credits')->default(3);
            $table->integer('hours_cm')->default(0);
            $table->integer('hours_td')->default(0);
            $table->integer('hours_tp')->default(0);
            $table->enum('evaluation_type', ['CC', 'TP', 'EXAM', 'RATTRAPAGE'])->default('EXAM');
            $table->decimal('cc_weight', 3, 2)->default(0.40);
            $table->decimal('exam_weight', 3, 2)->default(0.60);
            $table->decimal('tp_weight', 3, 2)->default(0.00);
            $table->boolean('is_optional')->default(false);
            $table->enum('regime', ['MANDATORY', 'OPTIONAL', 'ELECTIVE'])->default('MANDATORY');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_unit_id')->references('id')->on('course_units')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecues');
    }
};
