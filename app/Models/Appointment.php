namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'devis_habitation_id',
        'appointment_date',
        'appointment_time',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function devis()
    {
        return $this->belongsTo(DevisHabitation::class, 'devis_habitation_id');
    }
}
