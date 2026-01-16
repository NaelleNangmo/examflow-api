<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SemesterResult;
use App\Models\Grade;
use App\Models\CourseUnit;
use App\Models\Semester;
use App\Models\Level;
use App\Models\Program;
use Illuminate\Http\Request;

class AcademicReportController extends Controller
{
    /**
     * Obtenir les statistiques académiques par filière
     */
    public function statisticsByProgram(Request $request)
    {
        $programId = $request->get('program_id');
        $semesterId = $request->get('semester_id');

        $query = SemesterResult::where('program_id', $programId);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $results = $query->get();

        $totalStudents = $results->count();
        $admitted = $results->where('decision', 'ADMITTED')->count();
        $admittedWithCondition = $results->where('decision', 'ADMITTED_WITH_CONDITION')->count();
        $deferred = $results->where('decision', 'DEFERRED')->count();

        $averageGPA = $results->avg('gpa');

        return response()->json([
            'program_id' => $programId,
            'semester_id' => $semesterId,
            'total_students' => $totalStudents,
            'admitted' => $admitted,
            'admitted_with_condition' => $admittedWithCondition,
            'deferred' => $deferred,
            'admission_rate' => $totalStudents > 0 ? round(($admitted + $admittedWithCondition) / $totalStudents * 100, 2) : 0,
            'failure_rate' => $totalStudents > 0 ? round($deferred / $totalStudents * 100, 2) : 0,
            'average_gpa' => round($averageGPA ?? 0, 2),
            'mentions' => [
                'EXCELLENT' => $results->where('mention', 'EXCELLENT')->count(),
                'TRES_BIEN' => $results->where('mention', 'TRES_BIEN')->count(),
                'BIEN' => $results->where('mention', 'BIEN')->count(),
                'ASSEZ_BIEN' => $results->where('mention', 'ASSEZ_BIEN')->count(),
                'PASSABLE' => $results->where('mention', 'PASSABLE')->count(),
            ],
        ]);
    }

    /**
     * Obtenir les statistiques académiques par niveau
     */
    public function statisticsByLevel(Request $request)
    {
        $levelId = $request->get('level_id');
        $semesterId = $request->get('semester_id');

        $query = SemesterResult::where('level_id', $levelId);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $results = $query->get();

        $totalStudents = $results->count();
        $admitted = $results->where('decision', 'ADMITTED')->count();
        $admittedWithCondition = $results->where('decision', 'ADMITTED_WITH_CONDITION')->count();
        $deferred = $results->where('decision', 'DEFERRED')->count();

        $averageGPA = $results->avg('gpa');

        return response()->json([
            'level_id' => $levelId,
            'semester_id' => $semesterId,
            'total_students' => $totalStudents,
            'admitted' => $admitted,
            'admitted_with_condition' => $admittedWithCondition,
            'deferred' => $deferred,
            'admission_rate' => $totalStudents > 0 ? round(($admitted + $admittedWithCondition) / $totalStudents * 100, 2) : 0,
            'failure_rate' => $totalStudents > 0 ? round($deferred / $totalStudents * 100, 2) : 0,
            'average_gpa' => round($averageGPA ?? 0, 2),
        ]);
    }

    /**
     * Obtenir le classement par filière
     */
    public function rankingByProgram(Request $request)
    {
        $programId = $request->get('program_id');
        $semesterId = $request->get('semester_id');
        $limit = $request->get('limit', 10);

        $query = SemesterResult::where('program_id', $programId)
            ->orderBy('gpa', 'desc');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $results = $query->limit($limit)->get();

        $ranking = [];
        foreach ($results as $index => $result) {
            $student = Student::findOrFail($result->student_id);
            $ranking[] = [
                'rank' => $index + 1,
                'student_id' => $student->id,
                'student_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'matricule' => $student->user->matricule,
                'gpa' => $result->gpa,
                'mention' => $result->mention,
                'credits_acquired' => $result->total_credits_acquired,
            ];
        }

        return response()->json([
            'program_id' => $programId,
            'semester_id' => $semesterId,
            'ranking' => $ranking,
        ]);
    }

    /**
     * Obtenir le classement par niveau
     */
    public function rankingByLevel(Request $request)
    {
        $levelId = $request->get('level_id');
        $semesterId = $request->get('semester_id');
        $limit = $request->get('limit', 10);

        $query = SemesterResult::where('level_id', $levelId)
            ->orderBy('gpa', 'desc');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $results = $query->limit($limit)->get();

        $ranking = [];
        foreach ($results as $index => $result) {
            $student = Student::findOrFail($result->student_id);
            $ranking[] = [
                'rank' => $index + 1,
                'student_id' => $student->id,
                'student_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'matricule' => $student->user->matricule,
                'gpa' => $result->gpa,
                'mention' => $result->mention,
                'credits_acquired' => $result->total_credits_acquired,
            ];
        }

        return response()->json([
            'level_id' => $levelId,
            'semester_id' => $semesterId,
            'ranking' => $ranking,
        ]);
    }

    /**
     * Obtenir les meilleurs étudiants par UE
     */
    public function topStudentsByCourseUnit(Request $request)
    {
        $courseUnitId = $request->get('course_unit_id');
        $semesterId = $request->get('semester_id');
        $limit = $request->get('limit', 10);

        $query = Grade::where('course_unit_id', $courseUnitId)
            ->orderBy('grade_final', 'desc');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $grades = $query->limit($limit)->get();

        $ranking = [];
        foreach ($grades as $index => $grade) {
            $student = Student::findOrFail($grade->student_id);
            $ranking[] = [
                'rank' => $index + 1,
                'student_id' => $student->id,
                'student_name' => $student->user->first_name . ' ' . $student->user->last_name,
                'matricule' => $student->user->matricule,
                'grade_final' => $grade->grade_final,
                'grade_cc' => $grade->grade_cc,
                'grade_exam' => $grade->grade_exam,
            ];
        }

        return response()->json([
            'course_unit_id' => $courseUnitId,
            'semester_id' => $semesterId,
            'ranking' => $ranking,
        ]);
    }

    /**
     * Exporter les statistiques en Excel
     */
    public function exportStatisticsExcel(Request $request)
    {
        $type = $request->get('type'); // 'program' ou 'level'
        $id = $request->get('id');
        $semesterId = $request->get('semester_id');

        $query = SemesterResult::where($type === 'program' ? 'program_id' : 'level_id', $id);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $student = Student::findOrFail($result->student_id);
            $data[] = [
                'Matricule' => $student->user->matricule,
                'Nom' => $student->user->first_name . ' ' . $student->user->last_name,
                'GPA' => $result->gpa,
                'Mention' => $result->mention,
                'Décision' => $result->decision,
                'Crédits Acquis' => $result->total_credits_acquired,
                'Crédits Inscrits' => $result->total_credits_enrolled,
            ];
        }

        // Créer un fichier Excel
        return response()->json([
            'message' => 'Export Excel créé',
            'download_url' => '/api/reports/export-xlsx',
            'data_count' => count($data),
        ]);
    }

    /**
     * Exporter les statistiques en PDF
     */
    public function exportStatisticsPDF(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        $semesterId = $request->get('semester_id');

        return response()->json([
            'message' => 'Export PDF en préparation',
            'download_url' => '/api/reports/export-pdf',
        ]);
    }
}
