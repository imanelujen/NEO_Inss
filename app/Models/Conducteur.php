<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conducteur extends Model
{
protected $fillable = ['bonus_malus', 'historique_accidents', 'date_obtention_permis'];
    protected $casts = [
        'date_obtention_permis' => 'date',
    ];

    public function devisAuto()
    {
        return $this->hasMany(DevisAuto::class, 'id_conducteur');
    }
}
