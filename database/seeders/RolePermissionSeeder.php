<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les permissions
        $permissions = [
            // Users
            'users.create',
            'users.read',
            'users.update',
            'users.delete',
            // Grades
            'grades.create',
            'grades.read',
            'grades.update',
            'grades.delete',
            'grades.validate',
            'grades.validate_pedagogical',
            'grades.validate_administrative',
            'grades.read:own',
            'grades.import',
            // Students
            'students.create',
            'students.read',
            'students.update',
            'students.delete',
            // Teachers
            'teachers.create',
            'teachers.read',
            'teachers.update',
            'teachers.delete',
            // Results
            'results.read',
            'results.calculate',
            'results.validate',
            'results.read:own',
            // Transcripts
            'transcripts.read',
            'transcripts.generate',
            'transcripts.read:own',
            // Requests
            'requests.create',
            'requests.read',
            'requests.update',
            'requests.approve',
            'requests.read:own',
            // Statistics
            'statistics.read',
            // Programs & Programs
            'programs.create',
            'programs.read',
            'programs.update',
            'programs.delete',
            // Course Units
            'course_units.create',
            'course_units.read',
            'course_units.update',
            'course_units.delete',
            // Activity logs
            'activity_logs.read',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles avec leurs permissions

        // ADMIN - accès complet
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $adminRole->givePermissionTo(Permission::all());

        // ACADEMIC_DIRECTOR - Responsable académique / Responsable de filière
        $academicDirRole = Role::firstOrCreate(['name' => 'ACADEMIC_DIRECTOR']);
        $academicDirRole->givePermissionTo([
            'users.read',
            'grades.read',
            'grades.validate_administrative',
            'students.read',
            'teachers.read',
            'results.read',
            'results.calculate',
            'results.validate',
            'transcripts.read',
            'transcripts.generate',
            'statistics.read',
            'programs.read',
            'programs.update',
            'course_units.read',
            'activity_logs.read',
        ]);

        // DEPARTMENT_HEAD - Chef de département
        $deptHeadRole = Role::firstOrCreate(['name' => 'DEPARTMENT_HEAD']);
        $deptHeadRole->givePermissionTo([
            'users.read',
            'grades.read',
            'grades.validate_pedagogical',
            'students.read',
            'teachers.read',
            'results.read',
            'results.calculate',
            'statistics.read',
            'programs.read',
            'course_units.read',
            'activity_logs.read',
        ]);

        // ACADEMIC_SECRETARY - Secrétariat académique
        $academicSecRole = Role::firstOrCreate(['name' => 'ACADEMIC_SECRETARY']);
        $academicSecRole->givePermissionTo([
            'users.read',
            'users.update',
            'grades.read',
            'grades.update',
            'grades.import',
            'students.read',
            'students.update',
            'teachers.read',
            'results.read',
            'transcripts.read',
            'transcripts.generate',
            'requests.read',
            'requests.update',
            'course_units.read',
            'activity_logs.read',
        ]);

        // COORDINATOR - Coordinateur de filière (deprecated but kept for compatibility)
        $coordinatorRole = Role::firstOrCreate(['name' => 'COORDINATOR']);
        $coordinatorRole->givePermissionTo([
            'grades.read',
            'grades.validate_pedagogical',
            'students.read',
            'results.read',
            'statistics.read',
            'programs.read',
        ]);

        // TEACHER - Enseignant
        $teacherRole = Role::firstOrCreate(['name' => 'TEACHER']);
        $teacherRole->givePermissionTo([
            'grades.create',
            'grades.read',
            'grades.update',
            'students.read',
            'results.read',
            'transcripts.read:own',
        ]);

        // STUDENT - Étudiant (consultation uniquement)
        $studentRole = Role::firstOrCreate(['name' => 'STUDENT']);
        $studentRole->givePermissionTo([
            'grades.read:own',
            'results.read:own',
            'transcripts.read:own',
            'requests.create',
            'requests.read:own',
        ]);
    }
}
