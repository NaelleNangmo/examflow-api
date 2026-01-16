<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => 'Département ' . fake()->words(2, true),
            'description' => fake()->sentence(),
            'status' => 'ACTIVE',
        ];
    }
}
