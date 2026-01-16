<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as StudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentRequest::with(['student.user', 'courseUnit', 'assignedTo']);

        if ($request->has('studentId')) {
            $query->where('student_id', $request->studentId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:GRADE_CONTESTATION,COPY_REVISION,FORCE_MAJEURE,DEROGATION,ADMINISTRATIVE_CLAIM,TRANSCRIPT_DUPLICATE,ATTESTATION',
            'subject' => 'required|string',
            'description' => 'required|string',
            'courseUnitId' => 'nullable|exists:course_units,id',
            'gradeId' => 'nullable|exists:grades,id',
            'attachments' => 'nullable|array',
        ]);

        $requestNumber = 'REQ-' . strtoupper(Str::random(8));

        $studentRequest = StudentRequest::create([
            'request_number' => $requestNumber,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'student_id' => $request->user()->student->id ?? null,
            'course_unit_id' => $validated['courseUnitId'] ?? null,
            'grade_id' => $validated['gradeId'] ?? null,
            'attachments' => $validated['attachments'] ?? [],
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'data' => $studentRequest,
            'message' => 'Demande créée avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $studentRequest = StudentRequest::with(['student.user', 'courseUnit', 'grade', 'assignedTo', 'histories'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $studentRequest,
        ]);
    }

    public function assign(Request $request, string $id)
    {
        $validated = $request->validate([
            'assignedTo' => 'required|exists:users,id',
        ]);

        $studentRequest = StudentRequest::findOrFail($id);
        $studentRequest->update([
            'assigned_to' => $validated['assignedTo'],
            'status' => 'IN_REVIEW',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande assignée avec succès',
        ]);
    }

    public function approve(Request $request, string $id)
    {
        $validated = $request->validate([
            'response' => 'required|string',
        ]);

        $studentRequest = StudentRequest::findOrFail($id);
        $studentRequest->update([
            'status' => 'APPROVED',
            'response' => $validated['response'],
            'response_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande approuvée avec succès',
        ]);
    }

    public function reject(Request $request, string $id)
    {
        $validated = $request->validate([
            'rejectionReason' => 'required|string',
        ]);

        $studentRequest = StudentRequest::findOrFail($id);
        $studentRequest->update([
            'status' => 'REJECTED',
            'rejection_reason' => $validated['rejectionReason'],
            'response_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande rejetée',
        ]);
    }
}
