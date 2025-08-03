<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Devis extends Model
{
    protected $fillable = [
        'date_creation', 'date_expiration', 'montant_base', 'garanties_incluses',
        'status', 'typedevis', 'id_simulationsession'
    ];
    protected $casts = [
        //'garanties_incluses' => 'array',
        'date_creation' => 'date',
        'date_expiration' => 'date',
    ];
    public function simulationSession()
    {
        return $this->belongsTo(SimulationSession::class, 'id_simulationsession');
    }
    public function devisAuto()
    {
        return $this->hasMany(DevisAuto::class, 'id_devis');
    }
    public function devisHabitation()
    {
        return $this->hasMany(DevisHabitation::class, 'id_devis');
    }
}
