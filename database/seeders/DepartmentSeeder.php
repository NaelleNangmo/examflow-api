<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'code' => 'INFO',
                'name' => 'Département d\'Informatique',
                'description' => 'Département dédié à l\'enseignement et la recherche en informatique',
            ],
            [
                'code' => 'GEST',
                'name' => 'Département de Gestion',
                'description' => 'Département de gestion et management',
            ],
            [
                'code' => 'DROIT',
                'name' => 'Département de Droit',
                'description' => 'Département de sciences juridiques',
            ],
            [
                'code' => 'COM',
                'name' => 'Département de Communication',
                'description' => 'Département de communication et journalisme',
            ],
            [
                'code' => 'COMPTA',
                'name' => 'Département de Comptabilité',
                'description' => 'Département de comptabilité et finance',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
