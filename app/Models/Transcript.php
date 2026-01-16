<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transcript extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_result_id',
        'transcript_type',
        'transcript_number',
        'issue_date',
        'issued_by',
        'signature_name',
        'signature_title',
        'is_official',
        'qr_code',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'is_official' => 'boolean',
        ];
    }

    // Relations
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semesterResult(): BelongsTo
    {
        return $this->belongsTo(SemesterResult::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
