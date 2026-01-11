<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        return AcademicYear::with('semesters')->get();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:academic_years,name']);
        return AcademicYear::create($request->all());
    }

    public function show(AcademicYear $academicYear)
    {
        return $academicYear->load('semesters');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $academicYear->update($request->all());
        return $academicYear;
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

      public function setCurrent($id)
    {
        $year = AcademicYear::findOrFail($id);

        if ($year->is_locked) {
            return response()->json([
                'message' => 'Cette année académique est verrouillée'
            ], 403);
        }

        DB::transaction(function () use ($year) {

            // Fermer toutes les autres années
            AcademicYear::where('id', '!=', $year->id)
                ->update([
                    'is_current' => false,
                    'is_locked'  => true
                ]);

            // Verrouiller les semestres des autres années
            Semester::where('academic_year_id', '!=', $year->id)
                ->update([
                    'is_current' => false,
                    'is_locked'  => true
                ]);

            // Activer l'année choisie
            $year->update([
                'is_current' => true,
                'is_locked'  => false
            ]);

            // Activer automatiquement le premier semestre
            Semester::where('academic_year_id', $year->id)
                ->orderBy('id')
                ->first()
                ?->update([
                    'is_current' => true,
                    'is_locked'  => false
                ]);
        });

        return response()->json([
            'message' => 'Année académique activée avec succès'
        ]);
    }
}
