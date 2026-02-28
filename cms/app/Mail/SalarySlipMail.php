<?php

namespace App\Mail;

use App\Models\SalaryRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalarySlipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $salaryRecord;

    public function __construct(SalaryRecord $salaryRecord)
    {
        $this->salaryRecord = $salaryRecord;
    }

    public function build()
    {
        $monthName = date('F', mktime(0, 0, 0, $this->salaryRecord->month, 1));
        
        return $this->subject("Salary Slip - {$monthName} {$this->salaryRecord->year}")
                    ->view('emails.salary-slip');
    }
}
