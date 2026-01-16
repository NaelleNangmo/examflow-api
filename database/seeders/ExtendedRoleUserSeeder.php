<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ExtendedRoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@iuc.edu.cm'],
            [
                'matricule' => 'ADMIN001',
                'first_name' => 'Admin',
                'last_name' => 'Système',
                'phone_primary' => '+237666666666',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['ADMIN']);

        // Academic Directors (Responsables académiques)
        $academicDir1 = User::firstOrCreate(
            ['email' => 'marc.essomba@iuc.edu.cm'],
            [
                'matricule' => 'ACAD001',
                'first_name' => 'Marc',
                'last_name' => 'ESSOMBA',
                'phone_primary' => '+237677777777',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
            ]
        );
        $academicDir1->syncRoles(['ACADEMIC_DIRECTOR']);

        // Department Heads (Chefs de département)
        $deptHead1 = User::firstOrCreate(
            ['email' => 'paul.nganou@iuc.edu.cm'],
            [
                'matricule' => 'DEPT001',
                'first_name' => 'Paul',
                'last_name' => 'NGANOU',
                'phone_primary' => '+237688888888',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
            ]
        );
        $deptHead1->syncRoles(['DEPARTMENT_HEAD']);

        // Academic Secretary (Secrétariat académique)
        $academicSec = User::firstOrCreate(
            ['email' => 'francoise.tagne@iuc.edu.cm'],
            [
                'matricule' => 'SEC001',
                'first_name' => 'Françoise',
                'last_name' => 'TAGNE',
                'phone_primary' => '+237699999999',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
            ]
        );
        $academicSec->syncRoles(['ACADEMIC_SECRETARY']);

        // Existing Teachers - sync their roles
        $teachers = [
            'jean.mbarga@iuc.edu.cm',
            'sophie.kamga@iuc.edu.cm',
            'paul.ngongang@iuc.edu.cm',
            'annick.tchouaffe@iuc.edu.cm',
        ];

        foreach ($teachers as $email) {
            $teacher = User::where('email', $email)->first();
            if ($teacher) {
                $teacher->syncRoles(['TEACHER']);
            }
        }

        // Existing Students - sync their roles
        $students = [
            'marie.nkondo@iuc.edu.cm',
            'david.yinda@iuc.edu.cm',
            'olivier.kemayou@iuc.edu.cm',
            'nadine.owona@iuc.edu.cm',
            'arnaud.tagne@iuc.edu.cm',
            'micheline.dzekem@iuc.edu.cm',
            'fabrice.nkoum@iuc.edu.cm',
            'sylvie.fone@iuc.edu.cm',
            'serge.tekwa@iuc.edu.cm',
            'christine.mfouapon@iuc.edu.cm',
        ];

        foreach ($students as $email) {
            $student = User::where('email', $email)->first();
            if ($student) {
                $student->syncRoles(['STUDENT']);
            }
        }
    }
}
