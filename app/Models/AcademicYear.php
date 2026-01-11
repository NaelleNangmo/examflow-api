<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_current', 'is_locked'];

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
}
