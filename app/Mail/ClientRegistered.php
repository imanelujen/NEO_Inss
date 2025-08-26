<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $password;

    public function __construct($client, $password)
    {
        $this->client = $client;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Bienvenue chez Neo Assurances')
            ->view('emails.client_registered')
            ->with([
                'name' => $this->client->name,
                'prenom' => $this->client->prenom,
                'email' => $this->client->email,
                'password' => $this->password,
            ]);
    }
}