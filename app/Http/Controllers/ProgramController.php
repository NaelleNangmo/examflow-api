<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    // Lister tous les programmes
    public function index()
    {
        $programs = Program::with('department', 'levels')->get();
        return response()->json($programs, 200);
    }

    // Créer un nouveau programme
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'credits' => 'required|integer|min:0',
            'diploma' => 'required|string|max:255',
        ]);

        $program = Program::create($request->all());

        return response()->json([
            'message' => 'Programme créé avec succès',
            'program' => $program
        ], 201);
    }

    // Afficher un programme
    public function show($id)
    {
        $program = Program::with('department', 'levels')->find($id);

        if (!$program) {
            return response()->json(['message' => 'Programme introuvable'], 404);
        }

        return response()->json($program, 200);
    }

    // Mettre à jour un programme
    public function update(Request $request, $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['message' => 'Programme introuvable'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => ['sometimes', 'string', 'max:20', Rule::unique('programs')->ignore($program->id)],
            'department_id' => 'sometimes|exists:departments,id',
            'credits' => 'sometimes|integer|min:0',
            'duration' => 'sometimes|integer|min:1',
            'diploma' => 'sometimes|string|max:255',
        ]);

        $program->update($request->all());

        return response()->json([
            'message' => 'Programme mis à jour',
            'program' => $program
        ], 200);
    }

    // Supprimer un programme
    public function destroy($id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json(['message' => 'Programme introuvable'], 404);
        }

        $program->delete();

        return response()->json(['message' => 'Programme supprimé'], 200);
    }
}
