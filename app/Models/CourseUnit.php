<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseUnit extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'level_id',
        'program_id',
        'semester_number',
        'credits',
        'coefficient',
        'hours_cm',
        'hours_td',
        'hours_tp',
        'ue_type',
        'regime',
        'is_capitalizable',
        'has_elimination_threshold',
        'elimination_threshold',
        'cc_weight',
        'exam_weight',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'coefficient' => 'decimal:2',
            'elimination_threshold' => 'decimal:2',
            'cc_weight' => 'decimal:2',
            'exam_weight' => 'decimal:2',
            'is_capitalizable' => 'boolean',
            'has_elimination_threshold' => 'boolean',
        ];
    }

    // Relations
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_assignments')
            ->withPivot('assignment_type')
            ->withTimestamps();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseUnitEnrollment::class);
    }
}
