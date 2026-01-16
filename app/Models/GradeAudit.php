<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeAudit extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'grade_audits';

    protected $fillable = [
        'grade_id',
        'student_id',
        'course_unit_id',
        'semester_id',
        'action_type', // CREATE, UPDATE, VALIDATE_PEDAGOGICAL, VALIDATE_ADMINISTRATIVE, REJECT
        'old_value',
        'new_value',
        'actor_id',
        'actor_role',
        'comment',
        'status', // PENDING, APPROVED, REJECTED
        'validation_level', // NONE, PEDAGOGICAL, ADMINISTRATIVE
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relations
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
