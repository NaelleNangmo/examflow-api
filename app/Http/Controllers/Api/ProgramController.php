<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('department', 'levels')->get();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:programs,code',
            'name' => 'required|string',
            'departmentId' => 'required|exists:departments,id',
            'duration' => 'required|integer|min:1',
            'totalCredits' => 'required|integer',
            'degreeType' => 'required|in:LICENSE,MASTER,DOCTORATE',
        ]);

        $program = Program::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'department_id' => $validated['departmentId'],
            'duration' => $validated['duration'],
            'total_credits' => $validated['totalCredits'],
            'degree_type' => $validated['degreeType'],
            'status' => 'ACTIVE',
        ]);

        return response()->json([
            'success' => true,
            'data' => $program,
            'message' => 'Programme créé avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $program = Program::with('department', 'levels', 'courseUnits')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:programs,code,' . $id,
            'name' => 'required|string',
            'departmentId' => 'required|exists:departments,id',
            'duration' => 'required|integer',
            'totalCredits' => 'required|integer',
            'degreeType' => 'required|in:LICENSE,MASTER,DOCTORATE',
        ]);

        $program->update($validated);

        return response()->json([
            'success' => true,
            'data' => $program->fresh(),
            'message' => 'Programme mis à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Programme supprimé avec succès',
        ]);
    }
}
