<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Année académique 2024-2025
        $academicYear = AcademicYear::create([
            'code' => '2024-2025',
            'name' => 'Année Académique 2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'status' => 'ACTIVE',
            'is_current' => true,
        ]);

        // Semestre 1
        Semester::create([
            'code' => 'S1-2024-2025',
            'name' => 'Semestre 1 - 2024-2025',
            'academic_year_id' => $academicYear->id,
            'semester_number' => 1,
            'start_date' => '2024-09-01',
            'end_date' => '2025-01-31',
            'registration_start' => '2024-08-15',
            'registration_end' => '2024-09-15',
            'exam_session_start' => '2025-01-10',
            'exam_session_end' => '2025-01-31',
            'makeup_session_start' => '2025-02-15',
            'makeup_session_end' => '2025-02-28',
            'status' => 'ACTIVE',
            'is_current' => true,
        ]);

        // Semestre 2
        Semester::create([
            'code' => 'S2-2024-2025',
            'name' => 'Semestre 2 - 2024-2025',
            'academic_year_id' => $academicYear->id,
            'semester_number' => 2,
            'start_date' => '2025-02-01',
            'end_date' => '2025-06-30',
            'registration_start' => '2025-01-15',
            'registration_end' => '2025-02-15',
            'exam_session_start' => '2025-06-10',
            'exam_session_end' => '2025-06-30',
            'makeup_session_start' => '2025-07-15',
            'makeup_session_end' => '2025-07-31',
            'status' => 'PLANNED',
            'is_current' => false,
        ]);
    }
}
