<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::with('academicYear');

        if ($request->has('academicYearId')) {
            $query->where('academic_year_id', $request->academicYearId);
        }

        if ($request->has('isCurrent')) {
            $query->where('is_current', $request->isCurrent === 'true');
        }

        $semesters = $query->orderBy('start_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $semesters,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:semesters,code',
            'name' => 'required|string',
            'academicYearId' => 'required|exists:academic_years,id',
            'semesterNumber' => 'required|integer|in:1,2',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'registrationStart' => 'required|date',
            'registrationEnd' => 'required|date',
            'examSessionStart' => 'required|date',
            'examSessionEnd' => 'required|date',
        ]);

        $semester = Semester::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'academic_year_id' => $validated['academicYearId'],
            'semester_number' => $validated['semesterNumber'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'registration_start' => $validated['registrationStart'],
            'registration_end' => $validated['registrationEnd'],
            'exam_session_start' => $validated['examSessionStart'],
            'exam_session_end' => $validated['examSessionEnd'],
            'status' => 'PLANNED',
            'is_current' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $semester,
            'message' => 'Semestre créé avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $semester = Semester::with('academicYear')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $semester,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:semesters,code,' . $id,
            'name' => 'required|string',
            'academicYearId' => 'required|exists:academic_years,id',
            'semesterNumber' => 'required|integer|in:1,2',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'registrationStart' => 'required|date',
            'registrationEnd' => 'required|date',
            'examSessionStart' => 'required|date',
            'examSessionEnd' => 'required|date',
            'status' => 'nullable|in:ACTIVE,CLOSED,PLANNED',
        ]);

        $semester->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'academic_year_id' => $validated['academicYearId'],
            'semester_number' => $validated['semesterNumber'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'registration_start' => $validated['registrationStart'],
            'registration_end' => $validated['registrationEnd'],
            'exam_session_start' => $validated['examSessionStart'],
            'exam_session_end' => $validated['examSessionEnd'],
            'status' => $validated['status'] ?? $semester->status,
        ]);

        return response()->json([
            'success' => true,
            'data' => $semester->fresh(),
            'message' => 'Semestre mis à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semestre supprimé avec succès',
        ]);
    }
}
