<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Semester;
use App\Models\CourseUnit;
use App\Models\SemesterResult;
use Illuminate\Support\Collection;

class GradeCalculationService
{
    /**
     * Calcul la moyenne pour une ECUE (Element Constitutif d'UE)
     */
    public function calculateECUEAverage(Grade $grade): float
    {
        $cc = $grade->grade_cc ?? 0;
        $exam = $grade->grade_exam ?? 0;
        $tp = $grade->grade_tp ?? 0;

        $courseUnit = $grade->courseUnit;
        $ccWeight = $courseUnit->cc_weight ?? 0.4;
        $examWeight = $courseUnit->exam_weight ?? 0.6;
        $tpWeight = $courseUnit->tp_weight ?? 0.0;

        $average = ($cc * $ccWeight) + ($exam * $examWeight) + ($tp * $tpWeight);
        return round($average, 2);
    }

    /**
     * Calcul la moyenne pour une UE (Unité d'Enseignement)
     */
    public function calculateUEAverage(Student $student, CourseUnit $courseUnit, Semester $semester): float
    {
        $grades = Grade::where('student_id', $student->id)
            ->where('course_unit_id', $courseUnit->id)
            ->where('semester_id', $semester->id)
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $totalWeightedGrade = 0;
        $totalWeight = 0;

        foreach ($grades as $grade) {
            $average = $this->calculateECUEAverage($grade);
            $weight = $grade->courseUnit->coefficient ?? 1;

            $totalWeightedGrade += $average * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($totalWeightedGrade / $totalWeight, 2) : 0;
    }

    /**
     * Calcul la moyenne semestrielle d'un étudiant
     */
    public function calculateSemesterAverage(Student $student, Semester $semester): float
    {
        $courseUnits = CourseUnit::where('level_id', $student->level_id)
            ->where('semester_number', $semester->number)
            ->get();

        if ($courseUnits->isEmpty()) {
            return 0;
        }

        $totalWeightedAverage = 0;
        $totalCredits = 0;

        foreach ($courseUnits as $courseUnit) {
            $average = $this->calculateUEAverage($student, $courseUnit, $semester);
            $credits = $courseUnit->credits ?? 3;

            $totalWeightedAverage += $average * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalWeightedAverage / $totalCredits, 2) : 0;
    }

    /**
     * Calcul la moyenne annuelle d'un étudiant
     */
    public function calculateAnnualAverage(Student $student, $academicYear): float
    {
        $semesters = Semester::all(); // Récupérer S1 et S2

        if ($semesters->isEmpty()) {
            return 0;
        }

        $totalAverage = 0;
        $semesterCount = 0;

        foreach ($semesters as $semester) {
            $average = $this->calculateSemesterAverage($student, $semester);
            $totalAverage += $average;
            $semesterCount++;
        }

        return $semesterCount > 0 ? round($totalAverage / $semesterCount, 2) : 0;
    }

    /**
     * Génère les résultats de semestre avec décision académique
     */
    public function generateSemesterResults(Student $student, Semester $semester, $academicYear): void
    {
        $gpa = $this->calculateSemesterAverage($student, $semester);

        // Calcul des crédits
        $courseUnits = CourseUnit::where('level_id', $student->level_id)
            ->where('semester_number', $semester->number)
            ->get();

        $totalCreditsEnrolled = $courseUnits->sum('credits');
        $totalCreditsAcquired = 0;

        foreach ($courseUnits as $courseUnit) {
            $grades = Grade::where('student_id', $student->id)
                ->where('course_unit_id', $courseUnit->id)
                ->where('semester_id', $semester->id)
                ->get();

            if (!$grades->isEmpty()) {
                $average = $this->calculateUEAverage($student, $courseUnit, $semester);
                if ($average >= 10) {
                    $totalCreditsAcquired += $courseUnit->credits;
                }
            }
        }

        // Décision académique
        $mention = $this->getMention($gpa);
        $decision = $this->getDecision($gpa);

        SemesterResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'semester_id' => $semester->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'program_id' => $student->program_id,
                'level_id' => $student->level_id,
                'total_credits_enrolled' => $totalCreditsEnrolled,
                'total_credits_acquired' => $totalCreditsAcquired,
                'gpa' => $gpa,
                'mention' => $mention,
                'decision' => $decision,
                'decision_comment' => $this->getDecisionComment($decision),
                'is_validated' => false,
            ]
        );
    }

    /**
     * Retourne la mention selon le GPA
     */
    private function getMention(float $gpa): string
    {
        return match (true) {
            $gpa >= 16 => 'EXCELLENT',
            $gpa >= 14 => 'TRES_BIEN',
            $gpa >= 12 => 'BIEN',
            $gpa >= 10 => 'ASSEZ_BIEN',
            default => 'PASSABLE'
        };
    }

    /**
     * Retourne la décision académique selon le GPA
     */
    private function getDecision(float $gpa): string
    {
        return match (true) {
            $gpa >= 12 => 'ADMITTED',
            $gpa >= 10 => 'ADMITTED_WITH_CONDITION',
            default => 'DEFERRED'
        };
    }

    /**
     * Retourne le commentaire de décision
     */
    private function getDecisionComment(string $decision): string
    {
        return match ($decision) {
            'ADMITTED' => 'Admis',
            'ADMITTED_WITH_CONDITION' => 'Admis avec compensation - À valider en session de rattrapage',
            'DEFERRED' => 'Ajourné - Doublage requis',
            default => 'En attente de validation'
        };
    }

    /**
     * Valide les notes manquantes pour un semestre et un niveau
     */
    public function validateMissingGrades(Semester $semester, $levelId): array
    {
        $courseUnits = CourseUnit::where('level_id', $levelId)
            ->where('semester_number', $semester->number)
            ->get();

        $students = Student::where('level_id', $levelId)->get();

        $missingGrades = [];

        foreach ($students as $student) {
            foreach ($courseUnits as $courseUnit) {
                $gradeExists = Grade::where('student_id', $student->id)
                    ->where('course_unit_id', $courseUnit->id)
                    ->where('semester_id', $semester->id)
                    ->exists();

                if (!$gradeExists) {
                    $missingGrades[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->user->first_name . ' ' . $student->user->last_name,
                        'course_unit_id' => $courseUnit->id,
                        'course_unit_code' => $courseUnit->code,
                        'course_unit_name' => $courseUnit->name,
                    ];
                }
            }
        }

        return $missingGrades;
    }
}
