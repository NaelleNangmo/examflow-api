<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use App\Models\CourseUnit;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        $gradeCC = fake()->randomFloat(2, 0, 20);
        $gradeExam = fake()->randomFloat(2, 0, 20);
        $gradeFinal = ($gradeCC * 0.4) + ($gradeExam * 0.6);

        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'student_id' => Student::factory(),
            'course_unit_id' => CourseUnit::factory(),
            'semester_id' => Semester::factory(),
            'session_type' => 'NORMAL',
            'grade_cc' => $gradeCC,
            'grade_exam' => $gradeExam,
            'grade_final' => round($gradeFinal, 2),
            'is_absent' => false,
            'absence_justified' => null,
            'is_fraud' => false,
            'status' => 'DRAFT',
            'entered_by' => User::factory(),
            'entered_at' => now(),
        ];
    }
}
