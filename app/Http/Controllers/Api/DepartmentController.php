<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json(Department::with(['teachers', 'programs'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:departments,code',
    ]);

    $department = Department::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Département créé avec succès',
        'data' => $department
    ], 201);
    }

    public function show(Department $department)
    {
        return response()->json($department->load(['teachers', 'programs']));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:departments,name,' . $department->id,
            'code' => 'sometimes|required|string|unique:departments,code,' . $department->id,
            'head_of_department' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return response()->json($department);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(['message' => 'Département supprimé avec succès']);
    }
}
