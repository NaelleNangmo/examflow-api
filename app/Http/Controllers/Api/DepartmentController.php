<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('head')->get();

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:departments,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'headId' => 'nullable|exists:users,id',
        ]);

        $department = Department::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['headId'] ?? null,
            'status' => 'ACTIVE',
        ]);

        return response()->json([
            'success' => true,
            'data' => $department,
            'message' => 'Département créé avec succès',
        ], 201);
    }

    public function show(string $id)
    {
        $department = Department::with('head', 'programs', 'teachers')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:departments,code,' . $id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'headId' => 'nullable|exists:users,id',
        ]);

        $department->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'head_id' => $validated['headId'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $department->fresh(),
            'message' => 'Département mis à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Département supprimé avec succès',
        ]);
    }
}
