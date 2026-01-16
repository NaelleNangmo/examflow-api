<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@iuc.edu.cm',
            'password' => Hash::make('password'),
            'status' => 'ACTIVE',
        ]);
        $user->assignRole('STUDENT');

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@iuc.edu.cm',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'email',
                        'firstName',
                        'lastName',
                    ],
                    'expiresIn',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@iuc.edu.cm',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_user_can_get_current_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'email',
                    'firstName',
                    'lastName',
                ],
            ]);
    }
}
