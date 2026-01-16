<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'matricule' => 'ADMIN001',
            'email' => 'admin@iuc.edu.cm',
            'first_name' => 'Admin',
            'last_name' => 'IUC',
            'gender' => 'M',
            'phone_primary' => '237 699 000 001',
            'birth_date' => '1980-01-15',
            'birth_place' => 'Yaoundé',
            'status' => 'ACTIVE',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'last_login_at' => now(),
        ]);
        $admin->assignRole('ADMIN');

        // Enseignants
        $teachersData = [
            ['first' => 'Jean', 'last' => 'MBARGA', 'phone' => '237 699 000 002', 'birth' => 'Douala', 'date' => '1985-03-20'],
            ['first' => 'Sophie', 'last' => 'KAMGA', 'phone' => '237 699 000 003', 'birth' => 'Bamenda', 'date' => '1988-05-10'],
            ['first' => 'Paul', 'last' => 'NGONGANG', 'phone' => '237 699 000 004', 'birth' => 'Yaoundé', 'date' => '1987-07-25'],
            ['first' => 'Annick', 'last' => 'TCHOUAFFE', 'phone' => '237 699 000 005', 'birth' => 'Garoua', 'date' => '1990-02-14'],
        ];

        foreach ($teachersData as $index => $data) {
            $teacher = User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'matricule' => 'TEACH00' . ($index + 1),
                'email' => strtolower($data['first'] . '.' . $data['last']) . '@iuc.edu.cm',
                'first_name' => $data['first'],
                'last_name' => $data['last'],
                'gender' => $index % 2 === 0 ? 'M' : 'F',
                'phone_primary' => $data['phone'],
                'birth_date' => $data['date'],
                'birth_place' => $data['birth'],
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'last_login_at' => now(),
            ]);
            $teacher->assignRole('TEACHER');
        }

        // Étudiants - 10 étudiants répartis sur L1, L2, L3
        $studentsData = [
            // L1
            ['first' => 'Marie', 'last' => 'NKONDO', 'phone' => '237 699 000 006', 'birth' => 'Yaoundé', 'date' => '2004-08-12', 'gender' => 'F', 'matricule' => 'IUC2024001'],
            ['first' => 'David', 'last' => 'YINDA', 'phone' => '237 699 000 007', 'birth' => 'Douala', 'date' => '2005-01-20', 'gender' => 'M', 'matricule' => 'IUC2024002'],
            ['first' => 'Olivier', 'last' => 'KEMAYOU', 'phone' => '237 699 000 008', 'birth' => 'Bamenda', 'date' => '2004-11-05', 'gender' => 'M', 'matricule' => 'IUC2024003'],
            ['first' => 'Nadine', 'last' => 'OWONA', 'phone' => '237 699 000 009', 'birth' => 'Yaoundé', 'date' => '2005-03-18', 'gender' => 'F', 'matricule' => 'IUC2024004'],
            // L2
            ['first' => 'Arnaud', 'last' => 'TAGNE', 'phone' => '237 699 000 010', 'birth' => 'Garoua', 'date' => '2003-06-08', 'gender' => 'M', 'matricule' => 'IUC2023001'],
            ['first' => 'Micheline', 'last' => 'DZEKEM', 'phone' => '237 699 000 011', 'birth' => 'Douala', 'date' => '2003-09-14', 'gender' => 'F', 'matricule' => 'IUC2023002'],
            ['first' => 'Fabrice', 'last' => 'NKOUM', 'phone' => '237 699 000 012', 'birth' => 'Yaoundé', 'date' => '2003-04-25', 'gender' => 'M', 'matricule' => 'IUC2023003'],
            // L3
            ['first' => 'Sylvie', 'last' => 'FONE', 'phone' => '237 699 000 013', 'birth' => 'Bamenda', 'date' => '2002-07-30', 'gender' => 'F', 'matricule' => 'IUC2022001'],
            ['first' => 'Serge', 'last' => 'TEKWA', 'phone' => '237 699 000 014', 'birth' => 'Douala', 'date' => '2002-10-11', 'gender' => 'M', 'matricule' => 'IUC2022002'],
            ['first' => 'Christine', 'last' => 'MFOUAPON', 'phone' => '237 699 000 015', 'birth' => 'Yaoundé', 'date' => '2002-02-28', 'gender' => 'F', 'matricule' => 'IUC2022003'],
        ];

        foreach ($studentsData as $data) {
            $student = User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'matricule' => $data['matricule'],
                'email' => strtolower($data['first'] . '.' . $data['last']) . '@iuc.edu.cm',
                'first_name' => $data['first'],
                'last_name' => $data['last'],
                'gender' => $data['gender'],
                'phone_primary' => $data['phone'],
                'birth_date' => $data['date'],
                'birth_place' => $data['birth'],
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'last_login_at' => now(),
            ]);
            $student->assignRole('STUDENT');
        }
    }
}
