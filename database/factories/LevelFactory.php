<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => fake()->randomElement(['L1', 'L2', 'L3', 'M1', 'M2']),
            'name' => fake()->words(2, true),
            'program_id' => Program::factory(),
            'year_number' => fake()->numberBetween(1, 5),
            'credits' => 60,
            'status' => 'ACTIVE',
        ];
    }
}
