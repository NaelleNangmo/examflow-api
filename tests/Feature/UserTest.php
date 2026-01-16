<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $user->assignRole($adminRole);
        return $user;
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(5)->create();
        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                ],
            ]);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;
        $studentRole = Role::firstOrCreate(['name' => 'STUDENT']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/users', [
                'matricule' => 'TEST001',
                'email' => 'newuser@iuc.edu.cm',
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => 'password123',
                'roles' => ['STUDENT'],
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;
        $user = User::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/users/{$user->id}", [
                'matricule' => $user->matricule,
                'email' => $user->email,
                'first_name' => 'Updated',
                'last_name' => 'Name',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_pagination_works(): void
    {
        User::factory()->count(25)->create();
        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/users?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 10);
    }
}
