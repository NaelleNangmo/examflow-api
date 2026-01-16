<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Level;
use Spatie\Permission\Models\Role;

class StudentTest extends TestCase
{
    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $user->assignRole($adminRole);
        return $user;
    }

    public function test_admin_can_list_students(): void
    {
        $program = Program::factory()->create();
        $level = Level::factory()->create(['program_id' => $program->id]);
        
        Student::factory()->count(5)->create([
            'program_id' => $program->id,
            'level_id' => $level->id,
        ]);

        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/students');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }

    public function test_admin_can_view_student_details(): void
    {
        $program = Program::factory()->create();
        $level = Level::factory()->create(['program_id' => $program->id]);
        $student = Student::factory()->create([
            'program_id' => $program->id,
            'level_id' => $level->id,
        ]);

        $admin = $this->createAdminUser();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'matricule',
                    'email',
                    'firstName',
                    'lastName',
                ],
            ]);
    }

    public function test_student_can_view_own_grades(): void
    {
        $program = Program::factory()->create();
        $level = Level::factory()->create(['program_id' => $program->id]);
        $student = Student::factory()->create([
            'program_id' => $program->id,
            'level_id' => $level->id,
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'STUDENT']);
        $student->user->assignRole($studentRole);
        $token = $student->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/students/{$student->id}/grades");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }
}
