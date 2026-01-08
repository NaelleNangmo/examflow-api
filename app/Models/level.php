<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',       // L1, L2, L3...
        'program_id', // relation avec le programme
    ];

    // Un niveau appartient à un programme
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
