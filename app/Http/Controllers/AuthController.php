<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * LOGIN
     * POST /api/login
     */
    public function login(Request $request)
    {
        // 1️⃣ Validation
        $request->validate([
            'email'      => ['required', 'email'],
            'password'   => ['required', 'string'],
            'rememberMe' => ['nullable', 'boolean'],
        ]);

        // 2️⃣ Recherche utilisateur
        $user = User::where('email', $request->email)->first();

        // 3️⃣ Vérification credentials
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        // 4️⃣ Vérification statut (optionnel mais PRO)
        if ($user->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => 'Compte désactivé',
            ], 403);
        }

        // 5️⃣ Suppression anciens tokens (sécurité)
        $user->tokens()->delete();

        // 6️⃣ Création token Sanctum
        $tokenName = 'auth_token';

        $token = $user->createToken($tokenName)->plainTextToken;

        // 7️⃣ Réponse standard
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id'         => $user->id,
                    'email'      => $user->email,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'roles'      => $user->getRoleNames(),
                ]
            ]
        ]);
    }

    /**
     * ME
     * GET /api/me
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * LOGOUT
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        // Supprime uniquement le token courant
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
}
