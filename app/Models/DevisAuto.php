<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisAuto extends Model
{
    protected $table = 'devis_auto';

    protected $fillable = [
        'id_devis',
        'id_vehicule',
        'id_conducteur',
        'formules_choisis',
    ];

    public function conducteur()
    {
        return $this->belongsTo(Conducteur::class, 'id_conducteur');
    }
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'id_vehicule');
    }
    public function devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis');
    }
}
