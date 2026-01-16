<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transcript;
use App\Models\SemesterResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TranscriptController extends Controller
{
    public function index(Request $request)
    {
        $query = Transcript::with(['student.user', 'semesterResult']);

        if ($request->has('studentId')) {
            $query->where('student_id', $request->studentId);
        }

        $transcripts = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transcripts->items(),
        ]);
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'studentId' => 'required|exists:students,id',
            'semesterResultId' => 'required|exists:semester_results,id',
            'transcriptType' => 'required|in:PROVISIONAL,FINAL,MAKEUP',
        ]);

        $result = SemesterResult::with(['student.user', 'semester', 'program'])->findOrFail($validated['semesterResultId']);

        $transcriptNumber = 'TR-' . strtoupper(uniqid());

        $transcript = Transcript::create([
            'student_id' => $validated['studentId'],
            'semester_result_id' => $validated['semesterResultId'],
            'transcript_type' => $validated['transcriptType'],
            'transcript_number' => $transcriptNumber,
            'issue_date' => now(),
            'issued_by' => $request->user()->id,
            'is_official' => $validated['transcriptType'] === 'FINAL',
        ]);

        return response()->json([
            'success' => true,
            'data' => $transcript,
            'message' => 'Relevé de notes généré avec succès',
        ], 201);
    }

    public function download(string $id)
    {
        $transcript = Transcript::with(['student.user', 'semesterResult'])->findOrFail($id);

        // Incrémenter le compteur de téléchargements
        $transcript->increment('download_count');

        // Générer le PDF
        $pdf = Pdf::loadView('transcripts.transcript', [
            'transcript' => $transcript,
            'result' => $transcript->semesterResult,
            'student' => $transcript->student,
        ]);

        return $pdf->download("releve-notes-{$transcript->transcript_number}.pdf");
    }
}
