<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => strtoupper(fake()->unique()->bothify('PROG-###')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'department_id' => Department::factory(),
            'duration' => 3,
            'total_credits' => 180,
            'degree_type' => 'LICENSE',
            'status' => 'ACTIVE',
        ];
    }
}
