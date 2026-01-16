<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $year = fake()->year();
        $nextYear = $year + 1;

        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'code' => "{$year}-{$nextYear}",
            'name' => "Année Académique {$year}-{$nextYear}",
            'start_date' => "{$year}-09-01",
            'end_date' => "{$nextYear}-08-31",
            'status' => 'ACTIVE',
            'is_current' => false,
        ];
    }
}
