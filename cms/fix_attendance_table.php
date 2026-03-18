<?php
/**
 * Quick Fix: Add shift column to attendance table
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== FIXING ATTENDANCE TABLE ===\n\n";

// Check if shift column exists
$columns = DB::select("SHOW COLUMNS FROM attendance LIKE 'shift'");

if (empty($columns)) {
    echo "❌ 'shift' column NOT found in attendance table\n";
    echo "Adding 'shift' column...\n";
    
    try {
        DB::statement("ALTER TABLE attendance ADD COLUMN shift ENUM('Day', 'Night') DEFAULT 'Day' AFTER attendance_date");
        echo "✅ 'shift' column added successfully!\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "✅ 'shift' column already exists!\n";
}

// Check if shift_status column exists
$columns = DB::select("SHOW COLUMNS FROM attendance LIKE 'shift_status'");

if (empty($columns)) {
    echo "\n❌ 'shift_status' column NOT found\n";
    echo "Adding 'shift_status' column...\n";
    
    try {
        DB::statement("ALTER TABLE attendance ADD COLUMN shift_status VARCHAR(50) AFTER shift");
        echo "✅ 'shift_status' column added successfully!\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ 'shift_status' column already exists!\n";
}

// Check if entry_time column exists
$columns = DB::select("SHOW COLUMNS FROM attendance LIKE 'entry_time'");

if (empty($columns)) {
    echo "\n❌ 'entry_time' column NOT found\n";
    echo "Adding 'entry_time' column...\n";
    
    try {
        DB::statement("ALTER TABLE attendance ADD COLUMN entry_time TIME NULL AFTER shift_status");
        echo "✅ 'entry_time' column added successfully!\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ 'entry_time' column already exists!\n";
}

// Check if exit_time column exists
$columns = DB::select("SHOW COLUMNS FROM attendance LIKE 'exit_time'");

if (empty($columns)) {
    echo "\n❌ 'exit_time' column NOT found\n";
    echo "Adding 'exit_time' column...\n";
    
    try {
        DB::statement("ALTER TABLE attendance ADD COLUMN exit_time TIME NULL AFTER entry_time");
        echo "✅ 'exit_time' column added successfully!\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ 'exit_time' column already exists!\n";
}

echo "\n=== VERIFICATION ===\n";
$result = DB::select("DESCRIBE attendance");

echo "\nAttendance table structure:\n";
foreach ($result as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\n✅ ALL DONE! Now try saving attendance again.\n";
