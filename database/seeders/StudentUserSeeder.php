<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'STUDENT']);

        User::updateOrCreate(
            ['email' => 'student@iuc.cm'],
            [
                'matricule' => 'STUD001',
                'first_name' => 'Etudiant',
                'last_name' => 'IUC',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole($role);
    }
}
