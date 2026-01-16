<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Program;
use App\Models\Level;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $licInfo = Program::where('code', 'LIC-INFO')->first();
        $licGest = Program::where('code', 'LIC-GEST')->first();

        // Répartition des étudiants
        $studentsMapping = [
            // L1 Informatique
            ['email' => 'marie.nkondo@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L1', 'promotion' => '2024'],
            ['email' => 'david.yinda@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L1', 'promotion' => '2024'],
            ['email' => 'olivier.kemayou@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L1', 'promotion' => '2024'],
            ['email' => 'nadine.owona@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L1', 'promotion' => '2024'],
            // L2 Informatique
            ['email' => 'arnaud.tagne@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L2', 'promotion' => '2023'],
            ['email' => 'micheline.dzekem@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L2', 'promotion' => '2023'],
            ['email' => 'fabrice.nkoum@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L2', 'promotion' => '2023'],
            // L3 Informatique
            ['email' => 'sylvie.fone@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L3', 'promotion' => '2022'],
            ['email' => 'serge.tekwa@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L3', 'promotion' => '2022'],
            ['email' => 'christine.mfouapon@iuc.edu.cm', 'program' => 'LIC-INFO', 'level' => 'L3', 'promotion' => '2022'],
        ];

        foreach ($studentsMapping as $mapping) {
            $user = User::where('email', $mapping['email'])->first();
            $program = Program::where('code', $mapping['program'])->first();
            $level = Level::where('code', $mapping['level'])->where('program_id', $program->id)->first();

            if ($user && $program && $level) {
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_status' => 'REGULAR',
                        'regime' => 'FULL_TIME',
                        'program_id' => $program->id,
                        'level_id' => $level->id,
                        'promotion' => $mapping['promotion'],
                        'enrollment_date' => now()->subYear(),
                        'expected_graduation_date' => now()->addYear(2),
                    ]
                );
            }
        }
    }
}
