<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisHabitation extends Model
{

    protected $table = 'devis_habitation';

    protected $fillable = [
        'id_devis',
        'id_logement',
        'formules_choisis',
    ];

    public function logement()
    {
        return $this->belongsTo(Logement::class, 'id_logement');
    }
    public function devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis');
    }
}
