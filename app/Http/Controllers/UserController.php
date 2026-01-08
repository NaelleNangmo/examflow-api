<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::all()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => User::findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
        'email'      => 'required|email|unique:users,email',
        'matricule'  => 'required|string|unique:users,matricule',
        'first_name' => 'required|string|max:100',
        'last_name'  => 'required|string|max:100',
        'gender'     => 'required|in:M,F',
        'password'   => 'required|string|min:6',
        'role'       => 'required|string|exists:roles,name',
    ]);

    $user = User::create([
        'email'             => $validated['email'],
        'matricule'         => $validated['matricule'],
        'first_name'        => $validated['first_name'],
        'last_name'         => $validated['last_name'],
        'gender'            => $validated['gender'],
        'status'            => 'ACTIVE',
        'email_verified_at' => now(), // ✅ ICI
        'password'          => Hash::make($validated['password']),
    ]);

    $user->assignRole($validated['role']);

    return response()->json([
        'success' => true,
        'message' => 'Utilisateur créé et email vérifié automatiquement',
        'data'    => $user
    ], 201);
    }

    public function update($id)
    {
        return response()->json([
            'message' => "User {$id} updated (test)"
        ]);
    }

    public function activate($id)
    {
        return response()->json([
            'message' => "User {$id} activated"
        ]);
    }

    public function desactivate($id)
    {
        return response()->json([
            'message' => "User {$id} desactivated"
        ]);
    }
}

