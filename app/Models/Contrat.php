<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contrat_auto;

class Contrat extends Model
{
    protected $table = 'contrats';
    protected $fillable = [
        'type_contrat', 
        'id_client', 
        'id_devis', 
        'id_agent',
        'start_date', 
        'end_date', 
        'carte_grise_path',
        'prime', 'statut',
        'payment_frequency'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class, 'id_devis');
    }

    public function agent()
    {
        return $this->belongsTo(Agence::class, 'id_agent');
    }

    public function paiement()
    {
        return $this->belongsTo(Paiement::class, 'id_paiement');
    }
    public function contratAuto()
    {
        return $this->hasOne(Contrat_auto::class, 'id_contrat');
    }

    public function contratHabitation()
    {
        return $this->hasOne(ContratHabitation::class, 'id_contrat');
    }
}
