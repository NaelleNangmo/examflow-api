<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('level_id');
            $table->uuid('program_id');
            $table->integer('semester_number'); // 1 ou 2
            $table->integer('credits');
            $table->decimal('coefficient', 5, 2);
            $table->integer('hours_cm')->default(0); // Cours Magistraux
            $table->integer('hours_td')->default(0); // Travaux Dirigés
            $table->integer('hours_tp')->default(0); // Travaux Pratiques
            $table->enum('ue_type', ['FUNDAMENTAL', 'METHODOLOGY', 'TRANSVERSAL', 'OPTIONAL'])->default('FUNDAMENTAL');
            $table->enum('regime', ['MANDATORY', 'OPTIONAL', 'ELECTIVE'])->default('MANDATORY');
            $table->boolean('is_capitalizable')->default(true);
            $table->boolean('has_elimination_threshold')->default(false);
            $table->decimal('elimination_threshold', 5, 2)->nullable();
            $table->decimal('cc_weight', 3, 2)->default(0.40); // Poids du contrôle continu
            $table->decimal('exam_weight', 3, 2)->default(0.60); // Poids de l'examen
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_units');
    }
};
