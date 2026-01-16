<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Student;
use App\Models\CourseUnit;
use App\Models\Semester;
use App\Models\User;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with(['level', 'program'])->get();
        $semesters = Semester::all();

        // Get a teacher to mark as entered_by
        $teacher = User::whereHas('roles', function ($q) {
            $q->where('name', 'TEACHER');
        })->first();

        if ($students->isEmpty() || $semesters->isEmpty() || !$teacher) {
            return;
        }

        // Create grades for each student
        foreach ($students as $student) {
            // Get only course units for this student's level
            $courseUnitsForLevel = CourseUnit::where('level_id', $student->level_id)->get();

            if ($courseUnitsForLevel->isEmpty()) {
                continue;
            }

            // For each semester
            foreach ($semesters as $semester) {
                // Get only course units for this semester
                $courseUnitsForSemester = $courseUnitsForLevel->where('semester_number', $semester->number);

                foreach ($courseUnitsForSemester as $courseUnit) {
                    // Check if grade already exists
                    $existingGrade = Grade::where('student_id', $student->id)
                        ->where('course_unit_id', $courseUnit->id)
                        ->where('semester_id', $semester->id)
                        ->first();

                    if (!$existingGrade) {
                        // Create realistic grades (70% pass, 30% fail)
                        $isPass = rand(1, 100) <= 70;

                        if ($isPass) {
                            // Passing grades: 10-20 for CC, 8-20 for exam
                            $gradeCc = rand(11, 19);
                            $gradeExam = rand(10, 20);
                        } else {
                            // Failing grades: lower values
                            $gradeCc = rand(5, 10);
                            $gradeExam = rand(3, 8);
                        }

                        $gradeFinal = round(($gradeCc * $courseUnit->cc_weight) + ($gradeExam * $courseUnit->exam_weight), 2);

                        Grade::create([
                            'student_id' => $student->id,
                            'course_unit_id' => $courseUnit->id,
                            'semester_id' => $semester->id,
                            'grade_cc' => $gradeCc,
                            'grade_exam' => $gradeExam,
                            'grade_final' => $gradeFinal,
                            'status' => $isPass ? 'FINAL' : 'FINAL',
                            'entered_by' => $teacher->id,
                            'entered_at' => now()->subMonths(rand(1, 3)),
                        ]);
                    }                }
            }
        }
    }
}
