<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HabitationQuoteMail extends Mailable
{
   public function __construct($quote, $offer)
    {
        $this->quote = $quote;
        $this->offer = $offer;
    }

    public function build()
    {
        return $this->subject('Votre Devis Habitation - Neo Assurances')
            ->view('emails.quote')
            ->attachData(
                PDF::loadView('habitation.pdf', ['quote' => $this->quote, 'offer' => $this->offer])->output(),
                'devis_habitation_' . $this->quote->id . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
