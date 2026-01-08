<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    // Lister tous les niveaux
    public function index()
    {
        $levels = Level::with('program')->get();
        return response()->json($levels, 200);
    }

    // Créer un niveau
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:10',
            'program_id' => 'required|exists:programs,id'
        ]);

        $level = Level::create($request->all());

        return response()->json([
            'message' => 'Niveau créé avec succès',
            'level' => $level
        ], 201);
    }

    // Afficher un niveau
    public function show($id)
    {
        $level = Level::with('program')->find($id);

        if (!$level) {
            return response()->json(['message' => 'Niveau introuvable'], 404);
        }

        return response()->json($level, 200);
    }

    // Mettre à jour un niveau
    public function update(Request $request, $id)
    {
        $level = Level::find($id);

        if (!$level) {
            return response()->json(['message' => 'Niveau introuvable'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:10',
            'program_id' => 'sometimes|exists:programs,id',
            'semester_count' => 'sometimes|integer|min:1'
        ]);

        $level->update($request->all());

        return response()->json([
            'message' => 'Niveau mis à jour',
            'level' => $level
        ], 200);
    }

    // Supprimer un niveau
    public function destroy($id)
    {
        $level = Level::find($id);

        if (!$level) {
            return response()->json(['message' => 'Niveau introuvable'], 404);
        }

        $level->delete();

        return response()->json(['message' => 'Niveau supprimé'], 200);
    }
}
