<?php

namespace App\Mail;

use App\Models\Intern;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $intern;

    public function __construct(Intern $intern)
    {
        $this->intern = $intern;
    }

    public function build()
    {
        return $this->subject('Internship Completion Certificate - KWIKSTER')
                    ->view('emails.certificate')
                    ->with(['intern' => $this->intern]);
    }
}
