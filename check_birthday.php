<?php
// Quick script to check today's birthdays
// Run: php check_birthday.php

require __DIR__ . '/cms/vendor/autoload.php';

$app = require_once __DIR__ . '/cms/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;

$today = date('m-d');
echo "Today's date (MM-DD): " . $today . "\n\n";

$birthdays = Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [$today])
    ->whereNotNull('dob')
    ->get(['id', 'first_name', 'last_name', 'full_name', 'department', 'dob']);

echo "Total birthdays today: " . $birthdays->count() . "\n\n";

if ($birthdays->count() > 0) {
    echo "Birthday Employees:\n";
    echo "==================\n";
    foreach ($birthdays as $emp) {
        echo "ID: {$emp->id}\n";
        echo "Name: " . ($emp->full_name ?? $emp->first_name . ' ' . $emp->last_name) . "\n";
        echo "Department: {$emp->department}\n";
        echo "DOB: {$emp->dob}\n";
        echo "---\n";
    }
} else {
    echo "No birthdays today.\n\n";
    echo "To test, run this SQL:\n";
    echo "UPDATE employees SET dob = '" . date('Y-m-d') . "' WHERE id = 1;\n";
}
