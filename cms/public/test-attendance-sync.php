<!DOCTYPE html>
<html>
<head>
    <title>Attendance Sync Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>Attendance Synchronization Test</h1>
    
    <?php
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    use Illuminate\Support\Facades\DB;
    
    $today = date('Y-m-d');
    
    echo "<h2>Test Date: $today</h2>";
    
    // Test 1: Check attendance_logs
    echo "<h3>1. Face Recognition Logs (attendance_logs table)</h3>";
    $logs = DB::table('attendance_logs')->where('date', $today)->get();
    
    if ($logs->count() > 0) {
        echo "<p class='success'>✓ Found {$logs->count()} face recognition records</p>";
        echo "<table>";
        echo "<tr><th>Employee ID</th><th>Status</th><th>Entry Time</th><th>Exit Time</th></tr>";
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>{$log->employee_id}</td>";
            echo "<td>{$log->shift_status}</td>";
            echo "<td>{$log->entry_time}</td>";
            echo "<td>{$log->exit_time}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No face recognition records found for today</p>";
    }
    
    // Test 2: Check employees
    echo "<h3>2. Employee Records</h3>";
    $employees = DB::table('employees')
        ->where('user_type', 'employee')
        ->where('employee_status', 'active')
        ->get(['id', 'employee_id', 'first_name', 'last_name', 'email']);
    
    echo "<p class='success'>✓ Found {$employees->count()} active employees</p>";
    echo "<table>";
    echo "<tr><th>DB ID</th><th>Employee ID</th><th>Name</th><th>Email</th></tr>";
    foreach ($employees->take(10) as $emp) {
        echo "<tr>";
        echo "<td>{$emp->id}</td>";
        echo "<td>{$emp->employee_id}</td>";
        echo "<td>{$emp->first_name} {$emp->last_name}</td>";
        echo "<td>{$emp->email}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 3: Check if JOIN works
    echo "<h3>3. JOIN Test (attendance_logs + employees)</h3>";
    $joined = DB::table('attendance_logs as al')
        ->leftJoin('employees as e', 'al.employee_id', '=', 'e.employee_id')
        ->select('al.*', 'e.id as emp_db_id', 'e.first_name', 'e.last_name')
        ->where('al.date', $today)
        ->get();
    
    if ($joined->count() > 0) {
        echo "<p class='success'>✓ JOIN successful! Found {$joined->count()} matched records</p>";
        echo "<table>";
        echo "<tr><th>Log Employee ID</th><th>Matched DB ID</th><th>Name</th><th>Status</th></tr>";
        foreach ($joined as $j) {
            $matched = $j->emp_db_id ? 'YES' : 'NO';
            $class = $j->emp_db_id ? 'success' : 'error';
            echo "<tr class='$class'>";
            echo "<td>{$j->employee_id}</td>";
            echo "<td>" . ($j->emp_db_id ?? 'NOT MATCHED') . "</td>";
            echo "<td>" . ($j->first_name ?? 'N/A') . " " . ($j->last_name ?? '') . "</td>";
            echo "<td>{$j->shift_status}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No matched records found</p>";
    }
    
    // Test 4: Check attendance table
    echo "<h3>4. Manual Attendance Records</h3>";
    $attendance = DB::table('attendance')->where('attendance_date', $today)->get();
    
    if ($attendance->count() > 0) {
        echo "<p class='success'>✓ Found {$attendance->count()} manual attendance records</p>";
        echo "<table>";
        echo "<tr><th>Employee DB ID</th><th>Status</th><th>Entry Time</th><th>Exit Time</th></tr>";
        foreach ($attendance as $att) {
            echo "<tr>";
            echo "<td>{$att->employee_id}</td>";
            echo "<td>" . ($att->shift_status ?? 'NULL') . "</td>";
            echo "<td>" . ($att->entry_time ?? 'NULL') . "</td>";
            echo "<td>" . ($att->exit_time ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No manual attendance records found for today</p>";
    }
    
    echo "<hr>";
    echo "<h3>Diagnosis:</h3>";
    
    if ($logs->count() == 0) {
        echo "<p class='error'>⚠ No face recognition data found. Make sure face attendance is being marked.</p>";
    }
    
    if ($joined->count() > 0 && $joined->first()->emp_db_id) {
        echo "<p class='success'>✓ Employee matching is working correctly!</p>";
    } else {
        echo "<p class='error'>⚠ Employee IDs are not matching. Check if employee_id column in employees table matches attendance_logs.employee_id</p>";
    }
    
    ?>
    
    <hr>
    <p><a href="/admin/attendance">← Back to Attendance Page</a></p>
</body>
</html>
