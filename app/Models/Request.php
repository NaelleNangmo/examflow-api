<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'request_number',
        'type',
        'subject',
        'description',
        'student_id',
        'course_unit_id',
        'grade_id',
        'attachments',
        'status',
        'assigned_to',
        'response',
        'response_date',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'response_date' => 'datetime',
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

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(RequestHistory::class);
    }
}
