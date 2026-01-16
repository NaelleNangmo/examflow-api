<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ECUE extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'ecues';

    protected $fillable = [
        'code',
        'name',
        'course_unit_id',
        'coefficient',
        'credits',
        'hours_cm',
        'hours_td',
        'hours_tp',
        'evaluation_type', // CC, TP, EXAM, RATTRAPAGE
        'cc_weight',
        'exam_weight',
        'tp_weight',
        'is_optional',
        'regime', // MANDATORY, OPTIONAL, ELECTIVE
        'description',
    ];

    protected function casts(): array
    {
        return [
            'cc_weight' => 'decimal:2',
            'exam_weight' => 'decimal:2',
            'tp_weight' => 'decimal:2',
            'is_optional' => 'boolean',
        ];
    }

    // Relations
    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }
}
