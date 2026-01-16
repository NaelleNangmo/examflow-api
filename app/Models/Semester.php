<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'academic_year_id',
        'semester_number',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'exam_session_start',
        'exam_session_end',
        'makeup_session_start',
        'makeup_session_end',
        'status',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_start' => 'date',
            'registration_end' => 'date',
            'exam_session_start' => 'date',
            'exam_session_end' => 'date',
            'makeup_session_start' => 'date',
            'makeup_session_end' => 'date',
            'is_current' => 'boolean',
        ];
    }

    // Relations
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function semesterResults(): HasMany
    {
        return $this->hasMany(SemesterResult::class);
    }
}
