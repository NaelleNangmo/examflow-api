<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter les colonnes manquantes à la table grades si elles n'existent pas
        if (!Schema::hasColumn('grades', 'grade_tp')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->decimal('grade_tp', 5, 2)->nullable()->after('grade_exam');
            });
        }

        if (!Schema::hasColumn('grades', 'validation_level')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->enum('validation_level', ['NONE', 'PEDAGOGICAL', 'ADMINISTRATIVE'])->default('NONE')->after('status');
            });
        }

        if (!Schema::hasColumn('grades', 'validation_status')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->enum('validation_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('validation_level');
            });
        }
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['grade_tp', 'validation_level', 'validation_status']);
        });
    }
};
