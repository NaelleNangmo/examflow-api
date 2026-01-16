<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseUnit;
use Illuminate\Http\Request;

class CourseUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseUnit::with(['level', 'program']);

        if ($request->has('programId')) {
            $query->where('program_id', $request->programId);
        }

        if ($request->has('levelId')) {
            $query->where('level_id', $request->levelId);
        }

        if ($request->has('semesterNumber')) {
            $query->where('semester_number', $request->semesterNumber);
        }

        $courseUnits = $query->get();

        return response()->json([
            'success' => true,
            'data' => $courseUnits,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:course_units,code',
            'name' => 'required|string',
            'levelId' => 'required|exists:levels,id',
            'programId' => 'required|exists:programs,id',
            'semesterNumber' => 'required|integer|in:1,2',
            'credits' => 'required|integer',
            'coefficient' => 'required|numeric',
            'hoursCM' => 'nullable|integer',
            'hoursTD' => 'nullable|integer',
            'hoursTP' => 'nullable|integer',
            'ueType' => 'required|in:FUNDAMENTAL,METHODOLOGY,TRANSVERSAL,OPTIONAL',
            'regime' => 'required|in:MANDATORY,OPTIONAL,ELECTIVE',
            'ccWeight' => 'nullable|numeric|min:0|max:1',
            'examWeight' => 'nullable|numeric|min:0|max:1',
        ]);

        $courseUnit = CourseUnit::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'level_id' => $validated['levelId'],
            'program_id' => $validated['programId'],
            'semester_number' => $validated['semesterNumber'],
            'credits' => $validated['credits'],
            'coefficient' => $validated['coefficient'],
            'hours_cm' => $validated['hoursCM'] ?? 0,
            'hours_td' => $validated['hoursTD'] ?? 0,
            'hours_tp' => $validated['hoursTP'] ?? 0,
            'ue_type' => $validated['ueType'],
            'regime' => $validated['regime'],
            'cc_weight' => $validated['ccWeight'] ?? 0.4,
            'exam_weight' => $validated['examWeight'] ?? 0.6,
            'status' => 'ACTIVE',
        ]);

        return response()->json([
            'success' => true,
            'data' => $courseUnit,
            'message' => 'Unité d\'enseignement créée avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $courseUnit = CourseUnit::with(['level', 'program', 'teachers'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $courseUnit,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $courseUnit = CourseUnit::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:course_units,code,' . $id,
            'name' => 'required|string',
            'levelId' => 'required|exists:levels,id',
            'programId' => 'required|exists:programs,id',
            'semesterNumber' => 'required|integer|in:1,2',
            'credits' => 'required|integer',
            'coefficient' => 'required|numeric',
            'hoursCM' => 'nullable|integer',
            'hoursTD' => 'nullable|integer',
            'hoursTP' => 'nullable|integer',
            'ueType' => 'required|in:FUNDAMENTAL,METHODOLOGY,TRANSVERSAL,OPTIONAL',
            'regime' => 'required|in:MANDATORY,OPTIONAL,ELECTIVE',
            'ccWeight' => 'nullable|numeric|min:0|max:1',
            'examWeight' => 'nullable|numeric|min:0|max:1',
        ]);

        $courseUnit->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'level_id' => $validated['levelId'],
            'program_id' => $validated['programId'],
            'semester_number' => $validated['semesterNumber'],
            'credits' => $validated['credits'],
            'coefficient' => $validated['coefficient'],
            'hours_cm' => $validated['hoursCM'] ?? 0,
            'hours_td' => $validated['hoursTD'] ?? 0,
            'hours_tp' => $validated['hoursTP'] ?? 0,
            'ue_type' => $validated['ueType'],
            'regime' => $validated['regime'],
            'cc_weight' => $validated['ccWeight'] ?? 0.4,
            'exam_weight' => $validated['examWeight'] ?? 0.6,
        ]);

        return response()->json([
            'success' => true,
            'data' => $courseUnit->fresh(),
            'message' => 'Unité d\'enseignement mise à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $courseUnit = CourseUnit::findOrFail($id);
        $courseUnit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unité d\'enseignement supprimée avec succès',
        ]);
    }

    public function statistics(string $id)
    {
        $courseUnit = CourseUnit::findOrFail($id);
        
        // Statistiques des notes
        $grades = $courseUnit->grades()
            ->where('status', 'FINAL')
            ->whereNotNull('grade_final')
            ->get();

        $total = $grades->count();
        $average = $total > 0 ? $grades->avg('grade_final') : 0;
        $passed = $grades->where('grade_final', '>=', 10)->count();
        $failed = $total - $passed;

        return response()->json([
            'success' => true,
            'data' => [
                'totalGrades' => $total,
                'average' => round($average, 2),
                'passed' => $passed,
                'failed' => $failed,
                'successRate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
            ],
        ]);
    }
}
