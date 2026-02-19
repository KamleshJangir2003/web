<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JoiningLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Joining Letter - Welcome to Kwikster')
                    ->view('emails.joining-letter')
                    ->with(['employee' => $this->employee]);
    }
}
