<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeStudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $matricule;

    public function __construct($user, $password, $matricule)
    {
        $this->user = $user;
        $this->password = $password;
        $this->matricule = $matricule;
    }

    public function build()
    {
        return $this->subject('🦈 Bienvenue à SHARKLINK - Vos identifiants de connexion')
                    ->view('emails.welcome-student');
    }
}