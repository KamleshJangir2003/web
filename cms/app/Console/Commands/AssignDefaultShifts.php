<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class AssignDefaultShifts extends Command
{
    protected $signature = 'employees:assign-shifts';
    protected $description = 'Assign default Day Shift to employees without shift';

    public function handle()
    {
        $count = Employee::where('employee_status', 'active')
            ->where('hired_status', 'hired')
            ->whereNull('shift_id')
            ->update(['shift_id' => 1]);

        $this->info("Assigned Day Shift to {$count} employees");
        return 0;
    }
}
