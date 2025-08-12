<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logement extends Model
{
   protected $fillable = [
        'housing_type', 'surface_area', 'housing_value', 'construction_year', 'ville',
        'rue', 'code_postal', 'occupancy_status'
    ];

    public function devisHabitation()
    {
        return $this->belongsTo(DevisHabitation::class, 'id_devis_habitation');
    }
}
