<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;

Route::get('/debug/rejected-employees', function() {
    $allEmployees = Employee::where('user_type', 'employee')->get(['id', 'full_name', 'action_status', 'hired_status', 'updated_at']);
    
    echo "<h2>All Employees Status:</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Action Status</th><th>Hired Status</th><th>Updated</th></tr>";
    
    foreach($allEmployees as $emp) {
        echo "<tr>";
        echo "<td>{$emp->id}</td>";
        echo "<td>{$emp->full_name}</td>";
        echo "<td>" . ($emp->action_status ?? 'NULL') . "</td>";
        echo "<td>" . ($emp->hired_status ?? 'NULL') . "</td>";
        echo "<td>{$emp->updated_at}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>Rejected Employees (action_status = 'rejected'):</h2>";
    $rejected = Employee::where('user_type', 'employee')
        ->where('action_status', 'rejected')
        ->get(['id', 'full_name', 'action_status', 'hired_status']);
    
    echo "Count: " . $rejected->count() . "<br>";
    foreach($rejected as $emp) {
        echo "- {$emp->full_name} (action_status: {$emp->action_status}, hired_status: {$emp->hired_status})<br>";
    }
});
