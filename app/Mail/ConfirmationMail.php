<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private string $clientEmail, private string $confirmationCode)
    {
    }

    public function build()
    {
        return $this->subject('Подтверждение адреса почты')
            ->view('email.confirmation', ['clientEmail' => $this->clientEmail, 'confirmationCode' => $this->confirmationCode]);
    }
}
