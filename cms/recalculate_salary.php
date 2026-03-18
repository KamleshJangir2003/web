<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\SalaryRecord;
use Carbon\Carbon;

echo "=== Recalculate Salary for March 2026 ===\n\n";

$month = 3;
$year = 2026;
$employee_id = 99;

$employee = Employee::find($employee_id);

if (!$employee) {
    echo "Employee not found!\n";
    exit;
}

echo "Employee: {$employee->first_name} {$employee->last_name}\n";
echo "In-Hand Salary: ₹{$employee->in_hand_salary}\n\n";

// Get attendance records
$attendanceRecords = Attendance::where('employee_id', $employee->id)
    ->whereYear('attendance_date', $year)
    ->whereMonth('attendance_date', $month)
    ->get();

echo "Total Attendance Records: {$attendanceRecords->count()}\n\n";

// Count by shift_status
$present = $attendanceRecords->where('shift_status', 'Present')->count();
$absent = $attendanceRecords->where('shift_status', 'Absent')->count();
$halfDay = $attendanceRecords->where('shift_status', 'Half Day')->count();
$paidLeave = $attendanceRecords->where('shift_status', 'Paid Leave')->count();
$holiday = $attendanceRecords->where('shift_status', 'Holiday')->count();
$weekOff = $attendanceRecords->where('shift_status', 'Week Off')->count();
$compOff = $attendanceRecords->where('shift_status', 'Comp Off')->count();

echo "Attendance Breakdown:\n";
echo "  Present: $present\n";
echo "  Absent: $absent\n";
echo "  Half Day: $halfDay\n";
echo "  Paid Leave: $paidLeave\n";
echo "  Holiday: $holiday\n";
echo "  Week Off: $weekOff\n";
echo "  Comp Off: $compOff\n\n";

// Calculate working days
$workingDays = $present + $paidLeave + $compOff + $weekOff + $holiday + ($halfDay * 0.5);

echo "Working Days Calculation:\n";
echo "  = $present (Present) + $paidLeave (Paid Leave) + $compOff (Comp Off) + $weekOff (Week Off) + $holiday (Holiday) + " . ($halfDay * 0.5) . " (Half Day)\n";
echo "  = $workingDays days\n\n";

// Calculate salary
$totalDaysInMonth = Carbon::create($year, $month)->daysInMonth;
$inHandSalary = $employee->in_hand_salary;
$perDaySalary = $inHandSalary / $totalDaysInMonth;
$earnedSalary = $workingDays * $perDaySalary;

// Calculate PF/ESI
function calculateGrossFromInHand($inHand) {
    $gross = $inHand;
    for ($i = 0; $i < 10; $i++) {
        $basic = $gross * 0.60;
        $pfBasic = ($basic >= 15000) ? 15000 : $basic;
        $employeePf = $pfBasic * 0.12;
        $employeeEsic = ($gross <= 21000) ? $gross * 0.0075 : 0;
        $calculatedInHand = $gross - $employeePf - $employeeEsic;
        if (abs($calculatedInHand - $inHand) < 0.01) break;
        $gross = $gross + ($inHand - $calculatedInHand);
    }
    return round($gross, 2);
}

$gross = calculateGrossFromInHand($inHandSalary);
$basic = $gross * 0.60;
$pfBasic = ($basic >= 15000) ? 15000 : $basic;
$employeePf = $pfBasic * 0.12;
$employerPf = $pfBasic * 0.13;

if ($gross <= 21000) {
    $employeeEsic = $gross * 0.0075;
    $employerEsic = $gross * 0.0325;
} else {
    $employeeEsic = 0;
    $employerEsic = 0;
}

$deduction = $inHandSalary - $earnedSalary;
$netSalary = $earnedSalary - $employeePf - $employeeEsic;

echo "Salary Calculation:\n";
echo "  Total Days in Month: $totalDaysInMonth\n";
echo "  Per Day Salary: ₹" . number_format($perDaySalary, 2) . "\n";
echo "  Earned Salary: ₹" . number_format($earnedSalary, 2) . " ($workingDays × " . number_format($perDaySalary, 2) . ")\n";
echo "  Employee PF: ₹" . number_format($employeePf, 2) . "\n";
echo "  Employee ESIC: ₹" . number_format($employeeEsic, 2) . "\n";
echo "  Deduction: ₹" . number_format($deduction, 2) . "\n";
echo "  Net Salary: ₹" . number_format($netSalary, 2) . "\n\n";

// Update salary record
$salaryRecord = SalaryRecord::where('employee_id', $employee->id)
    ->where('month', $month)
    ->where('year', $year)
    ->first();

if ($salaryRecord) {
    echo "Updating existing salary record...\n";
    $salaryRecord->update([
        'working_days' => $workingDays,
        'deduction' => $deduction,
        'employee_pf' => $employeePf,
        'employee_esi' => $employeeEsic,
        'employer_pf' => $employerPf,
        'employer_esi' => $employerEsic,
        'net_salary' => $netSalary,
    ]);
    echo "✅ Salary record updated successfully!\n";
} else {
    echo "❌ No salary record found to update!\n";
}

echo "\n=== Done ===\n";
