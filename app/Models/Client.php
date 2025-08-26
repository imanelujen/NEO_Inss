<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'phone',
        'password',
        'statut',
        'date_inscription',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function contrats(): HasMany
    {
        return $this->hasMany(Contrat::class, 'id_client');
    }

    protected function setPasswordAttribute($value): void
    {
        if ($value) {
            Log::debug('setPasswordAttribute called', ['value' => '****']);
            $this->attributes['password'] = Hash::make($value);
            Log::debug('Password hashed', ['hashed' => '****']);
        }
    }
}