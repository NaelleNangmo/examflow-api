<?php

namespace Database\Factories;

use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', '+1 year');
        $endDate = (clone $startDate)->modify('+4 months');

        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => fake()->unique()->bothify('S#-####-####'),
            'name' => fake()->words(3, true),
            'academic_year_id' => AcademicYear::factory(),
            'semester_number' => fake()->numberBetween(1, 2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_start' => (clone $startDate)->modify('-15 days'),
            'registration_end' => (clone $startDate)->modify('+15 days'),
            'exam_session_start' => (clone $endDate)->modify('-20 days'),
            'exam_session_end' => $endDate,
            'status' => 'ACTIVE',
            'is_current' => false,
        ];
    }
}
