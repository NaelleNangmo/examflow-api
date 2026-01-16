<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            ExtendedRoleUserSeeder::class,
            ProgramSeeder::class,
            AcademicYearSeeder::class,
            SemesterSeeder::class,
            CourseUnitSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            TeacherAssignmentSeeder::class,
            GradeSeeder::class,
            SemesterResultSeeder::class,
        ]);
    }
}
