<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationSession extends Model
{
    protected $guarded = [];
    protected $casts = [
        'donnees_temporaires' => 'array',
        'date_debut' => 'datetime',
        'date_dernier_acces' => 'datetime',
    ];
    public function devis()
    {
        return $this->hasMany(Devis::class, 'id_simulationsession');
    }
}
