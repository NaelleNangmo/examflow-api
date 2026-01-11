<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver toute année courante existante
        AcademicYear::query()->update(['is_current' => false]);

        // Créer l'année académique courante
        $year = AcademicYear::create([
            'name' => '2024-2025',
            'is_current' => false,
            'is_locked' => true,
        ]);

        // Créer les semestres
        Semester::create([
            'name' => 'Semestre 1',
            'academic_year_id' => $year->id,
            'is_current' => true,
            'is_locked' => false,
        ]);

        Semester::create([
            'name' => 'Semestre 2',
            'academic_year_id' => $year->id,
            'is_current' => false,
            'is_locked' => false,
        ]);
    }
}
