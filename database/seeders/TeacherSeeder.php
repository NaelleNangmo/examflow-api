<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Department;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $infoDept = Department::where('code', 'INFO')->first();
        $gestDept = Department::where('code', 'GEST')->first();

        // Teachers mapping
        $teachersMapping = [
            ['email' => 'jean.mbarga@iuc.edu.cm', 'dept' => 'INFO', 'specialty' => 'Systèmes d\'exploitation', 'grade' => 'LECTURER'],
            ['email' => 'sophie.kamga@iuc.edu.cm', 'dept' => 'INFO', 'specialty' => 'Bases de données', 'grade' => 'LECTURER'],
            ['email' => 'paul.ngongang@iuc.edu.cm', 'dept' => 'INFO', 'specialty' => 'Programmation Web', 'grade' => 'ASSISTANT'],
            ['email' => 'annick.tchouaffe@iuc.edu.cm', 'dept' => 'INFO', 'specialty' => 'Algorithmes', 'grade' => 'TUTOR'],
        ];

        foreach ($teachersMapping as $mapping) {
            $user = User::where('email', $mapping['email'])->first();
            $dept = Department::where('code', $mapping['dept'])->first();

            if ($user && $dept) {
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'grade' => $mapping['grade'],
                        'specialty' => $mapping['specialty'],
                        'teacher_type' => 'FULL_TIME',
                        'department_id' => $dept->id,
                        'hire_date' => now()->subYears(rand(2, 8)),
                    ]
                );
            }
        }
    }
}
