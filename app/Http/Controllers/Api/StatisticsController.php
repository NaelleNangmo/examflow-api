<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Program;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalDepartments = Department::count();
        $totalPrograms = Program::count();

        // Tendance des inscriptions (5 dernières années)
        $enrollmentTrend = Enrollment::select(
            DB::raw('YEAR(enrollment_date) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->limit(5)
        ->get()
        ->map(fn($e) => [
            'year' => (string)$e->year,
            'count' => $e->count,
        ])
        ->reverse()
        ->values();

        // Taux de réussite par département (optimisé)
        $successRateByDepartment = DB::table('departments')
            ->leftJoin('programs', 'departments.id', '=', 'programs.department_id')
            ->leftJoin('students', 'programs.id', '=', 'students.program_id')
            ->leftJoin('grades', 'students.id', '=', 'grades.student_id')
            ->select(
                'departments.id',
                'departments.name',
                DB::raw('COUNT(CASE WHEN grades.status = "FINAL" AND grades.grade_final IS NOT NULL THEN 1 END) as total_grades'),
                DB::raw('COUNT(CASE WHEN grades.status = "FINAL" AND grades.grade_final >= 10 THEN 1 END) as passed_grades')
            )
            ->groupBy('departments.id', 'departments.name')
            ->get()
            ->map(function ($dept) {
                $successRate = $dept->total_grades > 0 ? ($dept->passed_grades / $dept->total_grades) * 100 : 0;
                return [
                    'departmentId' => $dept->id,
                    'departmentName' => $dept->name,
                    'successRate' => round($successRate, 2),
                ];
            })
            ->filter(fn($d) => $d['successRate'] > 0)
            ->values();

        // Activités récentes (dernières 10)
        $recentActivities = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.description', 'activity_logs.performed_at', 'users.first_name', 'users.last_name')
            ->orderBy('activity_logs.performed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'description' => $a->description,
                'date' => \Carbon\Carbon::parse($a->performed_at)->format('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'totalDepartments' => $totalDepartments,
                'totalPrograms' => $totalPrograms,
                'enrollmentTrend' => $enrollmentTrend,
                'successRateByDepartment' => $successRateByDepartment,
                'recentActivities' => $recentActivities,
            ],
        ]);
    }
}
