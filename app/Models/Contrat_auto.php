<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat_auto extends Model
{
    protected $fillable = ['id_contrat', 'id_vehicule', 'id_conducteur', 'garanties', 'franchise'];

    protected $casts = ['garanties' => 'array'];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'id_vehicule');
    }

    public function conducteur()
    {
        return $this->belongsTo(Conducteur::class, 'id_conducteur');
    }
}
