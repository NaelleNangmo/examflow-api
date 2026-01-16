<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\AcademicYear;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = AcademicYear::latest('created_at')->first();

        if (!$currentYear) {
            return;
        }

        Semester::create([
            'code' => 'S1',
            'name' => 'Semester 1',
            'semester_number' => 1,
            'academic_year_id' => $currentYear->id,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->startOfYear()->addMonths(5),
            'status' => 'ACTIVE',
        ]);

        Semester::create([
            'code' => 'S2',
            'name' => 'Semester 2',
            'semester_number' => 2,
            'academic_year_id' => $currentYear->id,
            'start_date' => now()->startOfYear()->addMonths(6),
            'end_date' => now()->endOfYear(),
            'status' => 'ACTIVE',
        ]);
    }
}
