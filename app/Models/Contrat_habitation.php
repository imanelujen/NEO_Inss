<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat_habitation extends Model
{
    protected $table = 'contrat_habitation';
   protected $fillable = ['id_contrat', 'id_logement', 'franchise', 'garanties'];

    protected $casts = ['garanties' => 'array'];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function logement()
    {
        return $this->belongsTo(Logement::class, 'id_logement');
    }
}
