<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="IUC Academic Management API",
 *     description="API pour le système de gestion académique de l'Institut Universitaire de la Côte"
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Serveur de développement"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Connexion utilisateur",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@iuc.edu.cm"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="rememberMe", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'rememberMe' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if ($user->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est désactivé. Veuillez contacter l\'administration.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);

        $expiresIn = $request->rememberMe ? 60 * 24 * 30 : 60 * 24 * 8;
        $token = $user->createToken('auth-token', ['*'], now()->addMinutes($expiresIn))->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $this->formatUser($user),
                'expiresIn' => $expiresIn * 60 * 1000,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Déconnexion utilisateur",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Déconnexion réussie")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Rafraîchir le token",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Token rafraîchi")
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()?->delete();

        $token = $user->createToken('auth-token', ['*'], now()->addHours(8))->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $this->formatUser($user),
                'expiresIn' => 28800000,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/auth/me",
     *     summary="Obtenir l'utilisateur connecté",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Utilisateur connecté")
     * )
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatUser($request->user()),
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
            'birthDate' => $user->birth_date?->toISOString(),
            'birthPlace' => $user->birth_place,
            'phonePrimary' => $user->phone_primary,
            'phoneSecondary' => $user->phone_secondary,
            'photoUrl' => $user->photo_url,
            'status' => $user->status,
            'emailVerified' => $user->email_verified_at !== null,
            'roles' => $roles,
            'permissions' => $permissions,
            'createdAt' => $user->created_at->toISOString(),
        ];
    }
}
