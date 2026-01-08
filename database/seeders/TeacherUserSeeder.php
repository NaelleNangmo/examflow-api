<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'TEACHER']);

        User::updateOrCreate(
            ['email' => 'teacher@iuc.cm'],
            [
                'matricule' => 'TEACH001',
                'first_name' => 'Enseignant',
                'last_name' => 'IUC',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        )->assignRole($role);
    }
}
