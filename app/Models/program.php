<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',             // nom du programme
        'department_id',    // relation avec le département
        'credits',          // nombre de crédits
        'diploma',          // diplôme délivré
    ];

    // Un programme appartient à un département
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Un programme peut avoir plusieurs niveaux (L1, L2, ...)
    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
