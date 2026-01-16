<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_unit_id',
        'semester_id',
        'session_type',
        'grade_cc',
        'grade_exam',
        'grade_final',
        'is_absent',
        'absence_justified',
        'is_fraud',
        'fraud_description',
        'teacher_comment',
        'status',
        'entered_by',
        'entered_at',
        'validated_by',
        'validated_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'grade_cc' => 'decimal:2',
            'grade_exam' => 'decimal:2',
            'grade_final' => 'decimal:2',
            'is_absent' => 'boolean',
            'absence_justified' => 'boolean',
            'is_fraud' => 'boolean',
            'entered_at' => 'datetime',
            'validated_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    // Relations
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

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GradeHistory::class);
    }

    // Calcul de la note finale
    public function calculateFinalGrade(): float
    {
        if ($this->is_absent || !$this->grade_cc || !$this->grade_exam) {
            return 0;
        }

        $courseUnit = $this->courseUnit;
        $ccWeight = $courseUnit->cc_weight ?? 0.4;
        $examWeight = $courseUnit->exam_weight ?? 0.6;

        return round(($this->grade_cc * $ccWeight) + ($this->grade_exam * $examWeight), 2);
    }
}
