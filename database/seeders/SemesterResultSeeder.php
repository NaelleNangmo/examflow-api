<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SemesterResult;
use App\Models\Student;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Level;

class SemesterResultSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('code', '2024-2025')->first();
        $licInfo = Program::where('code', 'LIC-INFO')->first();
        $l1 = Level::where('code', 'L1')->where('program_id', $licInfo->id)->first();
        $l2 = Level::where('code', 'L2')->where('program_id', $licInfo->id)->first();
        $l3 = Level::where('code', 'L3')->where('program_id', $licInfo->id)->first();

        $s1 = Semester::where('code', 'S1')->first();
        $s2 = Semester::where('code', 'S2')->first();

        $students = Student::all();

        foreach ($students as $student) {
            // Semestre 1
            $gpa1 = rand(8, 18) + rand(0, 99) / 100;
            $mention1 = match (true) {
                $gpa1 >= 16 => 'EXCELLENT',
                $gpa1 >= 14 => 'TRES_BIEN',
                $gpa1 >= 12 => 'BIEN',
                $gpa1 >= 10 => 'ASSEZ_BIEN',
                default => 'PASSABLE'
            };
            $decision1 = $gpa1 >= 12 ? 'ADMITTED' : ($gpa1 >= 10 ? 'ADMITTED_WITH_CONDITION' : 'DEFERRED');

            SemesterResult::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'semester_id' => $s1->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'program_id' => $student->program_id,
                    'level_id' => $student->level_id,
                    'total_credits_enrolled' => 30,
                    'total_credits_acquired' => rand(15, 30),
                    'gpa' => $gpa1,
                    'rank' => rand(1, 30),
                    'total_students' => 30,
                    'mention' => $mention1,
                    'decision' => $decision1,
                    'decision_comment' => $decision1 === 'ADMITTED' ? 'Admis au semestre suivant' : ($decision1 === 'ADMITTED_WITH_CONDITION' ? 'À valider en session de rattrapage' : 'Ajourné'),
                    'is_validated' => true,
                    'validated_at' => now(),
                    'published_at' => now(),
                ]
            );

            // Semestre 2
            $gpa2 = rand(8, 18) + rand(0, 99) / 100;
            $mention2 = match (true) {
                $gpa2 >= 16 => 'EXCELLENT',
                $gpa2 >= 14 => 'TRES_BIEN',
                $gpa2 >= 12 => 'BIEN',
                $gpa2 >= 10 => 'ASSEZ_BIEN',
                default => 'PASSABLE'
            };
            $decision2 = $gpa2 >= 12 ? 'ADMITTED' : ($gpa2 >= 10 ? 'ADMITTED_WITH_CONDITION' : 'DEFERRED');

            SemesterResult::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'semester_id' => $s2->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'program_id' => $student->program_id,
                    'level_id' => $student->level_id,
                    'total_credits_enrolled' => 30,
                    'total_credits_acquired' => rand(15, 30),
                    'gpa' => $gpa2,
                    'rank' => rand(1, 30),
                    'total_students' => 30,
                    'mention' => $mention2,
                    'decision' => $decision2,
                    'decision_comment' => $decision2 === 'ADMITTED' ? 'Admis au niveau suivant' : ($decision2 === 'ADMITTED_WITH_CONDITION' ? 'À valider en session de rattrapage' : 'Ajourné'),
                    'is_validated' => true,
                    'validated_at' => now(),
                    'published_at' => now(),
                ]
            );
        }
    }
}

