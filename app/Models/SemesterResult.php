<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemesterResult extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_id',
        'academic_year_id',
        'program_id',
        'level_id',
        'total_credits_enrolled',
        'total_credits_acquired',
        'gpa',
        'rank',
        'total_students',
        'mention',
        'decision',
        'decision_comment',
        'is_validated',
        'validated_by',
        'validated_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'gpa' => 'decimal:2',
            'is_validated' => 'boolean',
            'validated_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // Relations
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }
}
