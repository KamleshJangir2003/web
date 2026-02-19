<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;

echo "Updating existing employees with employee_id...\n\n";

$employees = Employee::orderBy('id')->get();
$counter = 1;

foreach ($employees as $employee) {
    $employeeId = 'KOI' . str_pad($counter, 2, '0', STR_PAD_LEFT);
    $employee->employee_id = $employeeId;
    $employee->save();
    echo "Updated Employee ID {$employee->id} -> {$employeeId}\n";
    $counter++;
}

echo "\nDone! Total updated: " . $employees->count() . "\n";
