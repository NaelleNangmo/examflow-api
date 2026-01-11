<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        return Semester::with('academicYear')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        return Semester::create($request->all());
    }

    public function show(Semester $semester)
    {
        return $semester->load('academicYear');
    }

    public function update(Request $request, Semester $semester)
    {
        $semester->update($request->all());
        return $semester;
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }


     public function setCurrent($id)
    {
        $semester = Semester::findOrFail($id);

        if ($semester->is_locked) {
            return response()->json([
                'message' => 'Ce semestre est verrouillé'
            ], 403);
        }

        DB::transaction(function () use ($semester) {

            // Verrouiller tous les autres semestres de l'année
            Semester::where('academic_year_id', $semester->academic_year_id)
                ->where('id', '!=', $semester->id)
                ->update([
                    'is_current' => false,
                    'is_locked'  => true
                ]);

            // Activer le semestre choisi
            $semester->update([
                'is_current' => true,
                'is_locked'  => false
            ]);
        });

        return response()->json([
            'message' => 'Semestre activé avec succès'
        ]);
    }
}
