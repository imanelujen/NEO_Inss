<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'amount', 'payment_frequency', 'status', 'payment_method', 'payment_date'
    ];

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'id_paiement');
    }
}
