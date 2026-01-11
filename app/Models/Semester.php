<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['name', 'academic_year_id', 'is_current', 'is_locked'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
