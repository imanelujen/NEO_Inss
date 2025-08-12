<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Client extends Model implements JWTSubject
{
    protected $fillable = [
        'name', 'prenom', 'email', 'phone', 'password', 'statut', 'date_inscription'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
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
