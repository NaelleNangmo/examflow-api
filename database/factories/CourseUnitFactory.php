<?php

namespace Database\Factories;

use App\Models\CourseUnit;
use App\Models\Program;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseUnitFactory extends Factory
{
    protected $model = CourseUnit::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => strtoupper(fake()->unique()->bothify('INF###')),
            'name' => fake()->words(4, true),
            'description' => fake()->sentence(),
            'level_id' => Level::factory(),
            'program_id' => Program::factory(),
            'semester_number' => fake()->numberBetween(1, 2),
            'credits' => fake()->numberBetween(3, 6),
            'coefficient' => fake()->randomFloat(2, 1, 5),
            'hours_cm' => fake()->numberBetween(20, 40),
            'hours_td' => fake()->numberBetween(10, 30),
            'hours_tp' => fake()->numberBetween(0, 20),
            'ue_type' => fake()->randomElement(['FUNDAMENTAL', 'METHODOLOGY', 'TRANSVERSAL', 'OPTIONAL']),
            'regime' => 'MANDATORY',
            'cc_weight' => 0.4,
            'exam_weight' => 0.6,
            'status' => 'ACTIVE',
        ];
    }
}
