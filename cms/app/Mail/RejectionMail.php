<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $reason;

    public function __construct(Employee $employee, $reason)
    {
        $this->employee = $employee;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Application Status - ' . $this->employee->full_name)
                    ->view('emails.rejection-letter')
                    ->with([
                        'employee' => $this->employee,
                        'reason' => $this->reason
                    ]);
    }
}
