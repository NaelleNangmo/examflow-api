<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\CourseUnit;
use App\Models\TeacherAssignment;

class TeacherAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Enseignants existants
        $jean = Teacher::whereHas('user', fn($q) => $q->where('email', 'jean.mbarga@iuc.edu.cm'))->first();
        $sophie = Teacher::whereHas('user', fn($q) => $q->where('email', 'sophie.kamga@iuc.edu.cm'))->first();
        $paul = Teacher::whereHas('user', fn($q) => $q->where('email', 'paul.ngongang@iuc.edu.cm'))->first();
        $annick = Teacher::whereHas('user', fn($q) => $q->where('email', 'annick.tchouaffe@iuc.edu.cm'))->first();

        // Unités de cours existantes
        $courseUnits = CourseUnit::all();

        // Assignements: chaque enseignant avec 2-3 UE
        if ($jean && $courseUnits->count() > 0) {
            TeacherAssignment::updateOrCreate(
                ['teacher_id' => $jean->id, 'course_unit_id' => $courseUnits->first()->id],
                ['assignment_type' => 'RESPONSIBLE', 'created_at' => now()]
            );
            if ($courseUnits->count() > 2) {
                TeacherAssignment::updateOrCreate(
                    ['teacher_id' => $jean->id, 'course_unit_id' => $courseUnits->skip(2)->first()->id],
                    ['assignment_type' => 'RESPONSIBLE', 'created_at' => now()]
                );
            }
        }

        if ($sophie && $courseUnits->count() > 1) {
            TeacherAssignment::updateOrCreate(
                ['teacher_id' => $sophie->id, 'course_unit_id' => $courseUnits->skip(1)->first()->id],
                ['assignment_type' => 'RESPONSIBLE', 'created_at' => now()]
            );
            if ($courseUnits->count() > 3) {
                TeacherAssignment::updateOrCreate(
                    ['teacher_id' => $sophie->id, 'course_unit_id' => $courseUnits->skip(3)->first()->id],
                    ['assignment_type' => 'RESPONSIBLE', 'created_at' => now()]
                );
            }
        }

        if ($paul && $courseUnits->count() > 2) {
            TeacherAssignment::updateOrCreate(
                ['teacher_id' => $paul->id, 'course_unit_id' => $courseUnits->skip(2)->first()->id],
                ['assignment_type' => 'CO_TEACHER', 'created_at' => now()]
            );
        }

        if ($annick && $courseUnits->count() > 1) {
            TeacherAssignment::updateOrCreate(
                ['teacher_id' => $annick->id, 'course_unit_id' => $courseUnits->skip(1)->first()->id],
                ['assignment_type' => 'CO_TEACHER', 'created_at' => now()]
            );
        }
    }
}
