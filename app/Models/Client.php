<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Contrat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;


class Client extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $fillable = [
        'name', 'prenom', 'email', 'phone', 'password', 'statut', 'date_inscription'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function setPasswordAttribute($value)
    {
    Log::debug('setPasswordAttribute called', ['value' => $value]);

    if ($value) {
        $hashed = Hash::make($value);
        Log::debug('Password hashed', ['hashed' => $hashed]);
        $this->attributes['password'] = $hashed;
    }
    }
    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'id_client');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
