<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAssignment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'course_unit_id',
        'assignment_type',
    ];

    // Relations
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }
}
