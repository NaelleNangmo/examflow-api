<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\CourseUnit;
use App\Models\Semester;
use App\Models\Program;
use App\Models\Level;
use App\Models\Grade;
use Spatie\Permission\Models\Role;

class GradeTest extends TestCase
{
    private function createTeacherUser(): User
    {
        $user = User::factory()->create();
        $teacherRole = Role::firstOrCreate(['name' => 'TEACHER']);
        $user->assignRole($teacherRole);
        return $user;
    }

    public function test_teacher_can_create_grade(): void
    {
        $program = Program::factory()->create();
        $level = Level::factory()->create(['program_id' => $program->id]);
        $student = Student::factory()->create([
            'program_id' => $program->id,
            'level_id' => $level->id,
        ]);
        $courseUnit = CourseUnit::factory()->create([
            'program_id' => $program->id,
            'level_id' => $level->id,
        ]);
        $semester = Semester::factory()->create();

        $teacher = $this->createTeacherUser();
        $token = $teacher->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/grades', [
                'studentId' => $student->id,
                'courseUnitId' => $courseUnit->id,
                'semesterId' => $semester->id,
                'sessionType' => 'NORMAL',
                'gradeCC' => 14,
                'gradeExam' => 16,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }

    public function test_teacher_can_list_grades(): void
    {
        Grade::factory()->count(10)->create();

        $teacher = $this->createTeacherUser();
        $token = $teacher->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/grades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);
    }
}
