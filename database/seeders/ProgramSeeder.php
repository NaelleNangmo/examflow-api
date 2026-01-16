<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Level;
use App\Models\Department;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $infoDept = Department::where('code', 'INFO')->first();
        $gestDept = Department::where('code', 'GEST')->first();

        // Licence Informatique
        $licInfo = Program::create([
            'code' => 'LIC-INFO',
            'name' => 'Licence en Informatique',
            'description' => 'Formation en informatique et technologies de l\'information',
            'department_id' => $infoDept->id,
            'duration' => 3,
            'total_credits' => 180,
            'degree_type' => 'LICENSE',
            'status' => 'ACTIVE',
        ]);

        // Créer les niveaux L1, L2, L3
        foreach ([1 => 'L1', 2 => 'L2', 3 => 'L3'] as $year => $code) {
            Level::create([
                'code' => $code,
                'name' => "Niveau {$year}",
                'program_id' => $licInfo->id,
                'year_number' => $year,
                'credits' => 60,
                'status' => 'ACTIVE',
            ]);
        }

        // Licence Gestion
        $licGest = Program::create([
            'code' => 'LIC-GEST',
            'name' => 'Licence en Gestion',
            'description' => 'Formation en gestion d\'entreprise',
            'department_id' => $gestDept->id,
            'duration' => 3,
            'total_credits' => 180,
            'degree_type' => 'LICENSE',
            'status' => 'ACTIVE',
        ]);

        foreach ([1 => 'L1', 2 => 'L2', 3 => 'L3'] as $year => $code) {
            Level::create([
                'code' => $code,
                'name' => "Niveau {$year}",
                'program_id' => $licGest->id,
                'year_number' => $year,
                'credits' => 60,
                'status' => 'ACTIVE',
            ]);
        }
    }
}
