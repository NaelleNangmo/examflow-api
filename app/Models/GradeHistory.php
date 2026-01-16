<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'grade_id',
        'action',
        'performed_by',
        'old_value',
        'new_value',
        'comment',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'performed_at' => 'datetime',
        ];
    }

    // Relations
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
