<?php
/**
 * Fix Missing Employee IDs
 * This script will populate employee_id column if it's NULL
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX MISSING EMPLOYEE IDs ===\n\n";

// Check employees without employee_id
$employees = DB::table('employees')
    ->where('user_type', 'employee')
    ->where('employee_status', 'active')
    ->whereNull('employee_id')
    ->get(['id', 'first_name', 'last_name', 'email']);

if ($employees->count() == 0) {
    echo "✓ All employees already have employee_id assigned!\n";
    exit;
}

echo "Found {$employees->count()} employees without employee_id\n\n";

// Get the last employee_id number
$lastEmployeeId = DB::table('employees')
    ->whereNotNull('employee_id')
    ->where('employee_id', 'like', 'KIO%')
    ->orderBy('employee_id', 'desc')
    ->value('employee_id');

if ($lastEmployeeId) {
    $lastNumber = (int) substr($lastEmployeeId, 3);
    echo "Last employee_id: $lastEmployeeId (number: $lastNumber)\n";
} else {
    $lastNumber = 0;
    echo "No existing employee_ids found, starting from KIO01\n";
}

echo "\nAssigning employee_ids:\n";

foreach ($employees as $emp) {
    $lastNumber++;
    $newEmployeeId = 'KIO' . str_pad($lastNumber, 2, '0', STR_PAD_LEFT);
    
    DB::table('employees')
        ->where('id', $emp->id)
        ->update(['employee_id' => $newEmployeeId]);
    
    echo "  ✓ {$emp->first_name} {$emp->last_name} → $newEmployeeId\n";
}

echo "\n=== COMPLETE ===\n";
echo "Total assigned: {$employees->count()}\n";
echo "\nNow run the test page to verify sync is working!\n";
