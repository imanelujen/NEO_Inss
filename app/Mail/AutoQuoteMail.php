<?php

namespace App\Mail;
use Barryvdh\DomPDF\Facade\Pdf;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $offer;

    public function __construct($quote, $offer)
    {
        $this->quote = $quote;
        $this->offer = $offer;
    }

public function build()
{
    return $this->subject('Votre Devis Auto - Neo Assurances')
        ->view('emails.auto_quote')
        ->attachData(
            Pdf::loadView('auto.pdf', ['quote' => $this->quote, 'offer' => $this->offer])->output(),
            'devis_auto_' . $this->quote->id . '.pdf',
            ['mime' => 'application/pdf']
        );
}

}
