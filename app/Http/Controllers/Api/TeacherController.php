<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'department']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $teachers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $teachers->map(fn($teacher) => $this->formatTeacher($teacher)),
            'meta' => [
                'current_page' => $teachers->currentPage(),
                'total' => $teachers->total(),
            ],
        ]);
    }

    public function currentTeacher()
    {
        $userId = auth()->id();
        $teacher = Teacher::with(['user', 'department', 'courseUnits'])->where('user_id', $userId)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatTeacher($teacher),
        ]);
    }

    public function show(string $id)
    {
        $teacher = Teacher::with(['user', 'department', 'courseUnits'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatTeacher($teacher),
        ]);
    }

    public function assignments(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        $assignments = $teacher->assignments()
            ->with('courseUnit')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments->map(fn($assignment) => [
                'id' => $assignment->id,
                'teacherId' => $assignment->teacher_id,
                'teacherName' => $teacher->user->full_name,
                'courseUnitId' => $assignment->course_unit_id,
                'courseUnitCode' => $assignment->courseUnit->code,
                'courseUnitName' => $assignment->courseUnit->name,
                'assignmentType' => $assignment->assignment_type,
                'createdAt' => $assignment->created_at->toISOString(),
            ]),
        ]);
    }

    private function formatTeacher(Teacher $teacher): array
    {
        $user = $teacher->user;

        return [
            'id' => $teacher->id,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'fullName' => $user->full_name,
            'grade' => $teacher->grade,
            'specialty' => $teacher->specialty,
            'teacherType' => $teacher->teacher_type,
            'departmentId' => $teacher->department_id,
            'departmentName' => $teacher->department->name ?? null,
            'hireDate' => $teacher->hire_date->toISOString(),
        ];
    }
}
