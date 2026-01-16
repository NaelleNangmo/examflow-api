<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_status',
        'regime',
        'program_id',
        'level_id',
        'promotion',
        'enrollment_date',
        'expected_graduation_date',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'expected_graduation_date' => 'date',
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function semesterResults(): HasMany
    {
        return $this->hasMany(SemesterResult::class);
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function courseUnits(): BelongsToMany
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_enrollments')
            ->withPivot('semester_id', 'enrollment_date', 'status')
            ->withTimestamps();
    }
}
