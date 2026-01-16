<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeAudit;
use App\Models\Student;
use App\Models\Semester;
use App\Services\GradeCalculationService;
use Illuminate\Http\Request;

class GradeValidationController extends Controller
{
    protected $gradeCalculationService;

    public function __construct(GradeCalculationService $gradeCalculationService)
    {
        $this->gradeCalculationService = $gradeCalculationService;
    }

    /**
     * Récupérer les notes en attente de validation pédagogique
     */
    public function getPedagogicalValidationPending(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $levelId = $request->get('level_id');

        $query = GradeAudit::where('validation_level', 'NONE')
            ->orWhere('status', 'PENDING')
            ->where('action_type', '!=', 'VALIDATE_ADMINISTRATIVE');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($levelId) {
            $query->whereHas('courseUnit', fn($q) => $q->where('level_id', $levelId));
        }

        return response()->json([
            'data' => $query->with(['student', 'courseUnit', 'actor'])->get(),
            'count' => $query->count(),
        ]);
    }

    /**
     * Récupérer les notes en attente de validation administrative
     */
    public function getAdministrativeValidationPending(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $programId = $request->get('program_id');

        $query = GradeAudit::where('validation_level', 'PEDAGOGICAL')
            ->where('status', 'APPROVED');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($programId) {
            $query->whereHas('student', fn($q) => $q->where('program_id', $programId));
        }

        return response()->json([
            'data' => $query->with(['student', 'courseUnit', 'actor'])->get(),
            'count' => $query->count(),
        ]);
    }

    /**
     * Valider une note au niveau pédagogique (Chef de département)
     */
    public function validatePedagogical(Request $request)
    {
        $validated = $request->validate([
            'grade_audit_id' => 'required|uuid|exists:grade_audits,id',
            'comment' => 'nullable|string',
            'approve' => 'required|boolean',
        ]);

        $audit = GradeAudit::findOrFail($validated['grade_audit_id']);
        $user = auth()->user();

        if ($audit->validation_level !== 'NONE') {
            return response()->json(['message' => 'Cette note a déjà été validée'], 400);
        }

        $audit->update([
            'validation_level' => 'PEDAGOGICAL',
            'status' => $validated['approve'] ? 'APPROVED' : 'REJECTED',
            'comment' => $validated['comment'] ?? $audit->comment,
        ]);

        // Log l'action de validation
        GradeAudit::create([
            'grade_id' => $audit->grade_id,
            'student_id' => $audit->student_id,
            'course_unit_id' => $audit->course_unit_id,
            'semester_id' => $audit->semester_id,
            'action_type' => 'VALIDATE_PEDAGOGICAL',
            'actor_id' => $user->id,
            'actor_role' => $user->roles->first()->name,
            'status' => $validated['approve'] ? 'APPROVED' : 'REJECTED',
            'validation_level' => 'PEDAGOGICAL',
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => $validated['approve'] ? 'Note validée pédagogiquement' : 'Note rejetée',
            'data' => $audit->fresh(),
        ]);
    }

    /**
     * Valider une note au niveau administratif (Responsable académique)
     */
    public function validateAdministrative(Request $request)
    {
        $validated = $request->validate([
            'grade_audit_id' => 'required|uuid|exists:grade_audits,id',
            'comment' => 'nullable|string',
            'approve' => 'required|boolean',
        ]);

        $audit = GradeAudit::findOrFail($validated['grade_audit_id']);
        $user = auth()->user();

        if ($audit->validation_level !== 'PEDAGOGICAL') {
            return response()->json(['message' => 'Cette note doit d\'abord être validée pédagogiquement'], 400);
        }

        $audit->update([
            'validation_level' => 'ADMINISTRATIVE',
            'status' => $validated['approve'] ? 'APPROVED' : 'REJECTED',
            'comment' => $validated['comment'] ?? $audit->comment,
        ]);

        // Log l'action de validation
        GradeAudit::create([
            'grade_id' => $audit->grade_id,
            'student_id' => $audit->student_id,
            'course_unit_id' => $audit->course_unit_id,
            'semester_id' => $audit->semester_id,
            'action_type' => 'VALIDATE_ADMINISTRATIVE',
            'actor_id' => $user->id,
            'actor_role' => $user->roles->first()->name,
            'status' => $validated['approve'] ? 'APPROVED' : 'REJECTED',
            'validation_level' => 'ADMINISTRATIVE',
            'comment' => $validated['comment'],
        ]);

        // Si validé administrativement, générer les résultats de semestre
        if ($validated['approve']) {
            $grade = $audit->grade;
            if ($grade) {
                $student = Student::findOrFail($grade->student_id);
                $semester = Semester::findOrFail($grade->semester_id);
                $academicYear = $student->program->academicYears()->latest()->first();

                $this->gradeCalculationService->generateSemesterResults($student, $semester, $academicYear);
            }
        }

        return response()->json([
            'message' => $validated['approve'] ? 'Note validée administrativement' : 'Note rejetée',
            'data' => $audit->fresh(),
        ]);
    }

    /**
     * Obtenir le statut de validation d'une note
     */
    public function getValidationStatus($gradeId)
    {
        $audits = GradeAudit::where('grade_id', $gradeId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'grade_id' => $gradeId,
            'audits' => $audits,
            'current_validation_level' => $audits->first()?->validation_level ?? 'NONE',
            'current_status' => $audits->first()?->status ?? 'PENDING',
        ]);
    }
}
