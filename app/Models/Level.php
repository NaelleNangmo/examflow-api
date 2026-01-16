<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'program_id',
        'year_number',
        'credits',
        'status',
    ];

    // Relations
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function courseUnits(): HasMany
    {
        return $this->hasMany(CourseUnit::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
