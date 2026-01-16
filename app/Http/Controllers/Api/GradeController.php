<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\CourseUnit;
use App\Models\GradeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with(['student.user', 'courseUnit', 'semester']);

        if ($request->has('courseUnitId')) {
            $query->where('course_unit_id', $request->courseUnitId);
        }

        if ($request->has('semesterId')) {
            $query->where('semester_id', $request->semesterId);
        }

        if ($request->has('studentId')) {
            $query->where('student_id', $request->studentId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('sessionType')) {
            $query->where('session_type', $request->sessionType);
        }

        $perPage = min($request->get('per_page', 20), 100);
        $page = max($request->get('page', 1), 1);
        
        $grades = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $grades->map(fn($grade) => $this->formatGrade($grade)),
            'meta' => [
                'current_page' => $grades->currentPage(),
                'total' => $grades->total(),
                'per_page' => $grades->perPage(),
                'last_page' => $grades->lastPage(),
                'from' => $grades->firstItem(),
                'to' => $grades->lastItem(),
            ],
        ]);
    }

    public function show(string $id)
    {
        $grade = Grade::with(['student.user', 'courseUnit', 'semester', 'enteredBy', 'validatedBy'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studentId' => 'required|exists:students,id',
            'courseUnitId' => 'required|exists:course_units,id',
            'semesterId' => 'required|exists:semesters,id',
            'sessionType' => 'required|in:NORMAL,MAKEUP',
            'gradeCC' => 'nullable|numeric|min:0|max:20',
            'gradeExam' => 'nullable|numeric|min:0|max:20',
            'isAbsent' => 'boolean',
            'absenceJustified' => 'nullable|boolean',
            'isFraud' => 'boolean',
            'fraudDescription' => 'nullable|string',
            'teacherComment' => 'nullable|string',
        ]);

        $courseUnit = CourseUnit::findOrFail($validated['courseUnitId']);

        // Calculer la note finale
        $gradeFinal = null;
        if (!$validated['isAbsent'] && isset($validated['gradeCC']) && isset($validated['gradeExam'])) {
            $ccWeight = $courseUnit->cc_weight ?? 0.4;
            $examWeight = $courseUnit->exam_weight ?? 0.6;
            $gradeFinal = round(($validated['gradeCC'] * $ccWeight) + ($validated['gradeExam'] * $examWeight), 2);
        }

        $grade = Grade::create([
            'student_id' => $validated['studentId'],
            'course_unit_id' => $validated['courseUnitId'],
            'semester_id' => $validated['semesterId'],
            'session_type' => $validated['sessionType'],
            'grade_cc' => $validated['gradeCC'] ?? null,
            'grade_exam' => $validated['gradeExam'] ?? null,
            'grade_final' => $gradeFinal,
            'is_absent' => $validated['isAbsent'] ?? false,
            'absence_justified' => $validated['absenceJustified'] ?? null,
            'is_fraud' => $validated['isFraud'] ?? false,
            'fraud_description' => $validated['fraudDescription'] ?? null,
            'teacher_comment' => $validated['teacherComment'] ?? null,
            'status' => 'DRAFT',
            'entered_by' => $request->user()->id,
            'entered_at' => now(),
        ]);

        // Log de l'action
        $this->logGradeHistory($grade, 'CREATED', $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade),
            'message' => 'Note créée avec succès',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $grade = Grade::findOrFail($id);

        // Vérifier que la note peut être modifiée
        if (in_array($grade->status, ['FINAL', 'VALIDATED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette note ne peut plus être modifiée',
            ], 403);
        }

        $validated = $request->validate([
            'gradeCC' => 'nullable|numeric|min:0|max:20',
            'gradeExam' => 'nullable|numeric|min:0|max:20',
            'isAbsent' => 'boolean',
            'absenceJustified' => 'nullable|boolean',
            'isFraud' => 'boolean',
            'fraudDescription' => 'nullable|string',
            'teacherComment' => 'nullable|string',
        ]);

        $oldValues = $grade->toArray();

        $courseUnit = $grade->courseUnit;
        $gradeFinal = null;
        if (!$validated['isAbsent'] && isset($validated['gradeCC']) && isset($validated['gradeExam'])) {
            $ccWeight = $courseUnit->cc_weight ?? 0.4;
            $examWeight = $courseUnit->exam_weight ?? 0.6;
            $gradeFinal = round(($validated['gradeCC'] * $ccWeight) + ($validated['gradeExam'] * $examWeight), 2);
        }

        $grade->update([
            'grade_cc' => $validated['gradeCC'] ?? $grade->grade_cc,
            'grade_exam' => $validated['gradeExam'] ?? $grade->grade_exam,
            'grade_final' => $gradeFinal,
            'is_absent' => $validated['isAbsent'] ?? $grade->is_absent,
            'absence_justified' => $validated['absenceJustified'] ?? $grade->absence_justified,
            'is_fraud' => $validated['isFraud'] ?? $grade->is_fraud,
            'fraud_description' => $validated['fraudDescription'] ?? $grade->fraud_description,
            'teacher_comment' => $validated['teacherComment'] ?? $grade->teacher_comment,
        ]);

        // Log de l'action
        $this->logGradeHistory($grade, 'UPDATED', $request->user()->id, $oldValues, $grade->toArray());

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade->fresh()),
            'message' => 'Note mise à jour avec succès',
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'courseUnitId' => 'required|exists:course_units,id',
            'semesterId' => 'required|exists:semesters,id',
            'grades' => 'required|array',
            'grades.*.studentId' => 'required|exists:students,id',
            'grades.*.gradeCC' => 'nullable|numeric|min:0|max:20',
            'grades.*.gradeExam' => 'nullable|numeric|min:0|max:20',
        ]);

        $courseUnit = CourseUnit::findOrFail($validated['courseUnitId']);
        $ccWeight = $courseUnit->cc_weight ?? 0.4;
        $examWeight = $courseUnit->exam_weight ?? 0.6;

        DB::beginTransaction();
        try {
            $created = [];
            foreach ($validated['grades'] as $gradeData) {
                $gradeFinal = null;
                if (isset($gradeData['gradeCC']) && isset($gradeData['gradeExam'])) {
                    $gradeFinal = round(($gradeData['gradeCC'] * $ccWeight) + ($gradeData['gradeExam'] * $examWeight), 2);
                }

                $grade = Grade::create([
                    'student_id' => $gradeData['studentId'],
                    'course_unit_id' => $validated['courseUnitId'],
                    'semester_id' => $validated['semesterId'],
                    'session_type' => 'NORMAL',
                    'grade_cc' => $gradeData['gradeCC'] ?? null,
                    'grade_exam' => $gradeData['gradeExam'] ?? null,
                    'grade_final' => $gradeFinal,
                    'status' => 'DRAFT',
                    'entered_by' => $request->user()->id,
                    'entered_at' => now(),
                ]);

                $this->logGradeHistory($grade, 'CREATED', $request->user()->id);
                $created[] = $grade;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => collect($created)->map(fn($g) => $this->formatGrade($g)),
                'message' => count($created) . ' notes créées avec succès',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création des notes: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function submit(Request $request, string $id)
    {
        $grade = Grade::findOrFail($id);
        
        if ($grade->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Cette note ne peut plus être soumise',
            ], 403);
        }

        $grade->update(['status' => 'SUBMITTED']);
        $this->logGradeHistory($grade, 'SUBMITTED', $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade->fresh()),
            'message' => 'Note soumise avec succès',
        ]);
    }

    public function validate(Request $request, string $id)
    {
        $grade = Grade::findOrFail($id);
        
        if (!in_array($grade->status, ['SUBMITTED', 'PRE_VALIDATED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette note ne peut pas être validée',
            ], 403);
        }

        $grade->update([
            'status' => 'VALIDATED',
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        $this->logGradeHistory($grade, 'VALIDATED', $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade->fresh()),
            'message' => 'Note validée avec succès',
        ]);
    }

    public function reject(Request $request, string $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $grade = Grade::findOrFail($id);
        
        $grade->update([
            'status' => 'REJECTED',
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        $this->logGradeHistory($grade, 'REJECTED', $request->user()->id, null, null, $validated['reason']);

        return response()->json([
            'success' => true,
            'data' => $this->formatGrade($grade->fresh()),
            'message' => 'Note rejetée',
        ]);
    }

    public function history(string $id)
    {
        $grade = Grade::findOrFail($id);
        $histories = GradeHistory::where('grade_id', $id)
            ->with('performedBy')
            ->orderBy('performed_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $histories->map(fn($h) => [
                'id' => $h->id,
                'action' => $h->action,
                'performedBy' => $h->performed_by,
                'performedByName' => $h->performedBy->full_name ?? null,
                'performedAt' => $h->performed_at->toISOString(),
                'oldValue' => $h->old_value,
                'newValue' => $h->new_value,
                'comment' => $h->comment,
            ]),
        ]);
    }

    private function formatGrade(Grade $grade): array
    {
        return [
            'id' => $grade->id,
            'studentId' => $grade->student_id,
            'studentName' => $grade->student->user->full_name ?? null,
            'studentMatricule' => $grade->student->user->matricule ?? null,
            'courseUnitId' => $grade->course_unit_id,
            'courseUnitCode' => $grade->courseUnit->code ?? null,
            'courseUnitName' => $grade->courseUnit->name ?? null,
            'semesterId' => $grade->semester_id,
            'semesterCode' => $grade->semester->code ?? null,
            'sessionType' => $grade->session_type,
            'gradeCC' => $grade->grade_cc,
            'gradeExam' => $grade->grade_exam,
            'gradeFinal' => $grade->grade_final,
            'isAbsent' => $grade->is_absent,
            'absenceJustified' => $grade->absence_justified,
            'isFraud' => $grade->is_fraud,
            'fraudDescription' => $grade->fraud_description,
            'teacherComment' => $grade->teacher_comment,
            'status' => $grade->status,
            'enteredBy' => $grade->entered_by,
            'enteredByName' => $grade->enteredBy->full_name ?? null,
            'enteredAt' => $grade->entered_at?->toISOString(),
            'validatedBy' => $grade->validated_by,
            'validatedByName' => $grade->validatedBy->full_name ?? null,
            'validatedAt' => $grade->validated_at?->toISOString(),
            'rejectedBy' => $grade->rejected_by,
            'rejectedByName' => $grade->rejectedBy->full_name ?? null,
            'rejectedAt' => $grade->rejected_at?->toISOString(),
            'rejectionReason' => $grade->rejection_reason,
            'createdAt' => $grade->created_at->toISOString(),
        ];
    }

    private function logGradeHistory(Grade $grade, string $action, string $userId, ?array $oldValue = null, ?array $newValue = null, ?string $comment = null): void
    {
        GradeHistory::create([
            'grade_id' => $grade->id,
            'action' => $action,
            'performed_by' => $userId,
            'old_value' => $oldValue,
            'new_value' => $newValue ?? $grade->toArray(),
            'comment' => $comment,
            'performed_at' => now(),
        ]);
    }
}
