<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestorePasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private string $iin, private string $token)
    {
    }

    public function build()
    {
        return $this->subject(__('translations.restore password mail subject'))
            ->view('email.resetPassword', ['iin' => $this->iin, 'token' => $this->token]);
    }
}

