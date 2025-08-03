<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vehicule extends Model
{
    protected $fillable = [
        'id_devis_auto', 'vehicle_type', 'make', 'model', 'fuel_type',
        'tax_horsepower', 'vehicle_value', 'registration_date'
    ];
    protected $casts = [
        'registration_date' => 'date',
    ];
    public function devisAuto()
    {
        return $this->belongsTo(DevisAuto::class, 'id_devis_auto');
    }
}
