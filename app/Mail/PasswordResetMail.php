<?php

namespace App\Mail;

use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable {


    use Queueable, SerializesModels;

    public $passwordReset;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de contraseña',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.passwordResetMail');
    }
}
