<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use App\Models\Program;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => User::factory(),
            'student_status' => 'REGULAR',
            'regime' => 'FULL_TIME',
            'program_id' => Program::factory(),
            'level_id' => Level::factory(),
            'promotion' => (string) fake()->year(),
            'enrollment_date' => fake()->date(),
        ];
    }
}
