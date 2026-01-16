<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'program', 'level']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        if ($request->has('programId')) {
            $query->where('program_id', $request->programId);
        }

        if ($request->has('levelCode')) {
            $query->whereHas('level', function ($q) use ($request) {
                $q->where('code', $request->levelCode);
            });
        }

        if ($request->has('promotion')) {
            $query->where('promotion', $request->promotion);
        }

        $perPage = min($request->get('per_page', 20), 100);
        $page = max($request->get('page', 1), 1);

        $students = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $students->map(fn($student) => $this->formatStudent($student)),
            'meta' => [
                'current_page' => $students->currentPage(),
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'last_page' => $students->lastPage(),
                'from' => $students->firstItem(),
                'to' => $students->lastItem(),
            ],
        ]);
    }

    public function currentStudent()
    {
        $userId = auth()->id();
        $student = Student::with(['user', 'program', 'level'])->where('user_id', $userId)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatStudent($student),
        ]);
    }

    public function show(string $id)
    {
        $student = Student::with(['user', 'program', 'level'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatStudent($student),
        ]);
    }

    public function grades(string $id, Request $request)
    {
        $student = Student::findOrFail($id);

        $query = Grade::with(['courseUnit', 'semester'])
            ->where('student_id', $id);

        if ($request->has('semesterId')) {
            $query->where('semester_id', $request->semesterId);
        }

        $grades = $query->get();

        return response()->json([
            'success' => true,
            'data' => $grades->map(fn($grade) => [
                'id' => $grade->id,
                'courseUnitId' => $grade->course_unit_id,
                'courseUnitCode' => $grade->courseUnit->code,
                'courseUnitName' => $grade->courseUnit->name,
                'semesterId' => $grade->semester_id,
                'gradeCC' => $grade->grade_cc,
                'gradeExam' => $grade->grade_exam,
                'gradeFinal' => $grade->grade_final,
                'status' => $grade->status,
                'enteredAt' => $grade->entered_at?->toISOString(),
            ]),
        ]);
    }

    private function formatStudent(Student $student): array
    {
        $user = $student->user;

        return [
            'id' => $student->id,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'fullName' => $user->full_name,
            'gender' => $user->gender,
            'status' => $user->status,
            'emailVerified' => $user->email_verified_at !== null,
            'studentStatus' => $student->student_status,
            'regime' => $student->regime,
            'programId' => $student->program_id,
            'programName' => $student->program->name ?? null,
            'levelCode' => $student->level->code ?? null,
            'levelId' => $student->level_id,
            'promotion' => $student->promotion,
            'enrollmentDate' => $student->enrollment_date->toISOString(),
            'createdAt' => $student->created_at->toISOString(),
        ];
    }
}
