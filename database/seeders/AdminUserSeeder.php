<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que le rôle ADMIN existe
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);

        // Création ou mise à jour de l'admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@iuc.cm'],
            [
                'matricule' => 'ADMIN001',
                'first_name' => 'Admin',
                'last_name' => 'IUC',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Attribution du rôle ADMIN
        if (!$admin->hasRole('ADMIN')) {
            $admin->assignRole($adminRole);
        }
    }
}
