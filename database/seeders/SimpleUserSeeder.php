<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SimpleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les utilisateurs de test sans les rôles pour l'instant
        $users = [
            [
                'id' => '1',
                'matricule' => 'ADMIN001',
                'email' => 'admin@iuc.edu.cm',
                'first_name' => 'Admin',
                'last_name' => 'IUC',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'matricule' => 'TEACH001',
                'email' => 'teacher@iuc.edu.cm',
                'first_name' => 'Enseignant',
                'last_name' => 'Test',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '3',
                'matricule' => 'IUC2024001',
                'email' => 'student@iuc.edu.cm',
                'first_name' => 'Étudiant',
                'last_name' => 'Test',
                'gender' => 'M',
                'status' => 'ACTIVE',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('Utilisateurs de test créés avec succès!');
    }
}