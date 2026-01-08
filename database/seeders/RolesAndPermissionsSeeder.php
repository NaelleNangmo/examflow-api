<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */
            $permissions = [

                // Utilisateurs
                'users:create',
                'users:read',
                'users:update',
                'users:delete',
                'users:activate',
                'users:deactivate',

                // Étudiants
                'students:read',
                'students:create',
                'students:update',

                // Enseignants
                'teachers:read',
                'teachers:create',
                'teachers:update',

                // Notes
                'grades:read',
                'grades:create',
                'grades:update',
                'grades:validate',
                'grades:reject',

                // Départements & Programmes
                'departments:read',
                'departments:create',
                'departments:update',
                'programs:read',
                'programs:create',
                'programs:update',

                // UE
                'course-units:read',
                'course-units:create',
                'course-units:update',

                // Statistiques
                'statistics:read',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            $admin = Role::firstOrCreate(['name' => 'ADMIN']);
            $teacher = Role::firstOrCreate(['name' => 'TEACHER']);
            $student = Role::firstOrCreate(['name' => 'STUDENT']);

            /*
            |--------------------------------------------------------------------------
            | Attribution permissions
            |--------------------------------------------------------------------------
            */

            // ADMIN → toutes les permissions
            $admin->syncPermissions(Permission::all());

            // TEACHER
            $teacher->syncPermissions([
                'students:read',
                'grades:read',
                'grades:create',
                'grades:update',
            ]);

            // STUDENT
            $student->syncPermissions([
                'students:read',
                'grades:read',
            ]);
        });
    }
}
