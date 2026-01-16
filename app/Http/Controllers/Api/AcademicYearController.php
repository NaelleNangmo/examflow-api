<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('semesters')->orderBy('start_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $academicYears,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:academic_years,code',
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
        ]);

        $academicYear = AcademicYear::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'status' => 'PLANNED',
            'is_current' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $academicYear,
            'message' => 'Année académique créée avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $academicYear = AcademicYear::with('semesters')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $academicYear,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:academic_years,code,' . $id,
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'status' => 'nullable|in:ACTIVE,CLOSED,PLANNED',
        ]);

        $academicYear->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'status' => $validated['status'] ?? $academicYear->status,
        ]);

        return response()->json([
            'success' => true,
            'data' => $academicYear->fresh(),
            'message' => 'Année académique mise à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Année académique supprimée avec succès',
        ]);
    }
}
