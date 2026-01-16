<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SemesterResult;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = SemesterResult::with(['student.user', 'semester', 'program', 'level']);

        if ($request->has('semesterId')) {
            $query->where('semester_id', $request->semesterId);
        }

        if ($request->has('studentId')) {
            $query->where('student_id', $request->studentId);
        }

        if ($request->has('programId')) {
            $query->where('program_id', $request->programId);
        }

        $results = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'total' => $results->total(),
            ],
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'semesterId' => 'required|exists:semesters,id',
        ]);

        $semester = Semester::findOrFail($validated['semesterId']);

        // Récupérer tous les étudiants inscrits dans ce semestre
        $students = DB::table('course_unit_enrollments')
            ->where('semester_id', $semester->id)
            ->distinct()
            ->pluck('student_id');

        $calculated = 0;

        foreach ($students as $studentId) {
            $this->calculateStudentResult($studentId, $semester->id);
            $calculated++;
        }

        return response()->json([
            'success' => true,
            'message' => "Résultats calculés pour {$calculated} étudiants",
        ]);
    }

    private function calculateStudentResult(string $studentId, string $semesterId): void
    {
        $grades = Grade::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->where('status', 'FINAL')
            ->whereNotNull('grade_final')
            ->with('courseUnit')
            ->get();

        if ($grades->isEmpty()) {
            return;
        }

        $student = \App\Models\Student::findOrFail($studentId);

        $totalCredits = 0;
        $totalPoints = 0;
        $acquiredCredits = 0;

        foreach ($grades as $grade) {
            $courseUnit = $grade->courseUnit;
            $credits = $courseUnit->credits;
            $coefficient = $courseUnit->coefficient;
            $gradeValue = $grade->grade_final;

            $totalCredits += $credits;
            $totalPoints += $gradeValue * $coefficient;

            if ($gradeValue >= 10) {
                $acquiredCredits += $credits;
            }
        }

        $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

        // Déterminer la mention
        $mention = null;
        if ($gpa >= 16) {
            $mention = 'EXCELLENT';
        } elseif ($gpa >= 14) {
            $mention = 'TRES_BIEN';
        } elseif ($gpa >= 12) {
            $mention = 'BIEN';
        } elseif ($gpa >= 10) {
            $mention = 'ASSEZ_BIEN';
        } elseif ($gpa >= 8) {
            $mention = 'PASSABLE';
        }

        // Déterminer la décision
        $decision = 'DEFERRED';
        if ($gpa >= 10 && $acquiredCredits >= $totalCredits * 0.6) {
            $decision = 'ADMITTED';
        } elseif ($gpa >= 8) {
            $decision = 'ADMITTED_WITH_COMPENSATION';
        }

        // Calculer le rang
        $rank = SemesterResult::where('semester_id', $semesterId)
            ->where('gpa', '>', $gpa)
            ->count() + 1;

        $totalStudents = SemesterResult::where('semester_id', $semesterId)->count() + 1;

        SemesterResult::updateOrCreate(
            [
                'student_id' => $studentId,
                'semester_id' => $semesterId,
            ],
            [
                'academic_year_id' => $semester->academic_year_id,
                'program_id' => $student->program_id,
                'level_id' => $student->level_id,
                'total_credits_enrolled' => $totalCredits,
                'total_credits_acquired' => $acquiredCredits,
                'gpa' => $gpa,
                'rank' => $rank,
                'total_students' => $totalStudents,
                'mention' => $mention,
                'decision' => $decision,
            ]
        );
    }

    public function validate(Request $request, string $id)
    {
        $result = SemesterResult::findOrFail($id);

        $result->update([
            'is_validated' => true,
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Résultat validé avec succès',
        ]);
    }
}
