<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->has('role')) {
            $query->role($request->role);
        }

        // Filtre par statut
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = min($request->get('per_page', 20), 100); // Max 100 par page
        $page = max($request->get('page', 1), 1);
        
        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ]);
    }

    public function show(string $id)
    {
        $user = User::with(['teacher', 'student', 'roles', 'permissions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:users,matricule',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|in:M,F',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'matricule' => $validated['matricule'],
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'status' => 'ACTIVE',
        ]);

        $user->assignRole($validated['roles']);

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
            'message' => 'Utilisateur créé avec succès',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'matricule' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'nullable|string|min:8',
            'gender' => 'nullable|in:M,F',
            'status' => 'nullable|in:ACTIVE,INACTIVE,SUSPENDED,PENDING',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
            'message' => 'Utilisateur mis à jour avec succès',
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès',
        ]);
    }

    public function activate(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'ACTIVE']);

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
            'message' => 'Utilisateur activé avec succès',
        ]);
    }

    public function deactivate(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'INACTIVE']);

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
            'message' => 'Utilisateur désactivé avec succès',
        ]);
    }

    private function formatUser(User $user): array
    {
        $roles = $user->getRoleNames()->toArray();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        return [
            'id' => $user->id,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'fullName' => $user->full_name,
            'gender' => $user->gender,
            'status' => $user->status,
            'emailVerified' => $user->email_verified_at !== null,
            'roles' => $roles,
            'permissions' => $permissions,
            'createdAt' => $user->created_at->toISOString(),
        ];
    }
}
