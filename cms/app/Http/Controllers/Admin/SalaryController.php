<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\SalaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\SalarySlipMail;
use Illuminate\Support\Facades\Mail;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        // Load salary records from database
        $salaryRecords = SalaryRecord::with(['employee' => function($query) {
            $query->select('id', 'first_name', 'last_name', 'job_title', 'department', 'shift', 'current_ctc', 'in_hand_salary');
        }])
        ->where('month', $month)
        ->where('year', $year)
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Check for newly generated salaries
        $newSalaries = $this->checkForNewSalaries();

        return view('admin.salary.index', compact('salaryRecords', 'month', 'year', 'newSalaries'));
    }
    
    private function checkForNewSalaries()
    {
        $lastMonth = Carbon::now()->subMonth();
        $newSalaries = SalaryRecord::with('employee')
            ->where('month', $lastMonth->month)
            ->where('year', $lastMonth->year)
            ->where('created_at', '>=', Carbon::now()->subDays(7)) // Check last 7 days
            ->get();
            
        return $newSalaries;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        $month = $request->month;
        $year = $request->year;

        // Get all employees - Remove basic_salary filter temporarily
        $employees = Employee::where('user_type', 'employee')->get();

        if ($employees->isEmpty()) {
            return redirect()->route('admin.salary.index', ['month' => $month, 'year' => $year])
                ->with('error', 'No employees found!');
        }

        $totalDaysInMonth = Carbon::create($year, $month)->daysInMonth;
        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($employees as $employee) {
            // Use only in_hand_salary for calculation (no fallback to basic_salary)
            $inHandSalary = $employee->in_hand_salary;
            
            if (!$inHandSalary || $inHandSalary <= 0) {
                $skippedCount++;
                continue; // Skip employees without in_hand_salary
            }

            // Check if salary already generated
            $existingRecord = SalaryRecord::where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingRecord) {
                $skippedCount++;
                continue; // Skip if already generated
            }

            // Calculate salary components using same logic as salary calculator
            $gross = $this->calculateGrossFromInHand($inHandSalary);
            $basic = $gross * 0.60;
            $hra = $gross * 0.40;
            
            // PF calculations with cap rule
            $pfBasic = ($basic >= 15000) ? 15000 : $basic;
            $employeePf = $pfBasic * 0.12;
            $employerPf = $pfBasic * 0.13;
            
            // ESIC calculations (only if Gross <= 21000)
            if ($gross <= 21000) {
                $employeeEsic = $gross * 0.0075;
                $employerEsic = $gross * 0.0325;
            } else {
                $employeeEsic = 0;
                $employerEsic = 0;
            }
            
            // CTC = Gross + Employer contributions
            $ctc = $gross + $employerPf + $employerEsic;

            // Get attendance records for the month
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month)
                ->get();

            // Count different attendance statuses
            $present = $attendanceRecords->where('status', 'Present')->count();
            $absent = $attendanceRecords->where('status', 'Absent')->count();
            $halfDay = $attendanceRecords->where('status', 'Half Day')->count();
            $unauthorizedLeave = $attendanceRecords->where('status', 'Unauthorized Leave')->count();
            $paidLeave = $attendanceRecords->where('status', 'Paid Leave')->count();
            $holiday = $attendanceRecords->where('status', 'Holiday')->count();
            $weekOff = $attendanceRecords->where('status', 'Week Off')->count();
            $compOff = $attendanceRecords->where('status', 'Comp Off')->count();

            // Calculate working days
            $workingDays = $present + $paidLeave + $compOff + ($halfDay * 0.5);

            // Calculate per day salary
            $perDaySalary = $inHandSalary / $totalDaysInMonth;

            // Calculate salary based on working days only (proportional)
            $earnedSalary = $workingDays * $perDaySalary;
            
            // No deductions - just pay for actual working days
            $deduction = $inHandSalary - $earnedSalary;
            $netSalary = $earnedSalary;

            // Create salary record with proper breakdown
            SalaryRecord::create([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'basic_salary' => $inHandSalary, // Store in_hand_salary amount
                'working_days' => $workingDays,
                'deduction' => $deduction,
                'advance' => 0, // Default values
                'incentive' => 0,
                'employee_pf' => $employeePf,
                'employee_esi' => $employeeEsic,
                'employer_pf' => $employerPf,
                'employer_esi' => $employerEsic,
                'net_salary' => $netSalary,
                'shift' => $employee->shift ?? 'Day'
            ]);

            $generatedCount++;
        }

        $message = "Salary generated for {$generatedCount} employees successfully!";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} already existed)";
        }

        return redirect()->route('admin.salary.index', ['month' => $month, 'year' => $year])
            ->with('success', $message);
    }

    public function view($id)
    {
        $salaryRecord = SalaryRecord::with('employee')->findOrFail($id);
        
        // Get detailed attendance breakdown
        $attendanceRecords = Attendance::where('employee_id', $salaryRecord->employee_id)
            ->whereYear('attendance_date', $salaryRecord->year)
            ->whereMonth('attendance_date', $salaryRecord->month)
            ->get();

        $attendanceBreakdown = [
            'Present' => $attendanceRecords->where('status', 'Present')->count(),
            'Absent' => $attendanceRecords->where('status', 'Absent')->count(),
            'Half Day' => $attendanceRecords->where('status', 'Half Day')->count(),
            'Unauthorized Leave' => $attendanceRecords->where('status', 'Unauthorized Leave')->count(),
            'Paid Leave' => $attendanceRecords->where('status', 'Paid Leave')->count(),
            'Holiday' => $attendanceRecords->where('status', 'Holiday')->count(),
            'Week Off' => $attendanceRecords->where('status', 'Week Off')->count(),
            'Comp Off' => $attendanceRecords->where('status', 'Comp Off')->count(),
        ];

        return view('admin.salary.view', compact('salaryRecord', 'attendanceBreakdown'));
    }

    public function slip($id)
    {
        $salaryRecord = SalaryRecord::with('employee')->findOrFail($id);
        
        // Get detailed attendance breakdown
        $attendanceRecords = Attendance::where('employee_id', $salaryRecord->employee_id)
            ->whereYear('attendance_date', $salaryRecord->year)
            ->whereMonth('attendance_date', $salaryRecord->month)
            ->get();

        $attendanceBreakdown = [
            'Present' => $attendanceRecords->where('status', 'Present')->count(),
            'Absent' => $attendanceRecords->where('status', 'Absent')->count(),
            'Half Day' => $attendanceRecords->where('status', 'Half Day')->count(),
            'Unauthorized Leave' => $attendanceRecords->where('status', 'Unauthorized Leave')->count(),
            'Paid Leave' => $attendanceRecords->where('status', 'Paid Leave')->count(),
            'Holiday' => $attendanceRecords->where('status', 'Holiday')->count(),
            'Week Off' => $attendanceRecords->where('status', 'Week Off')->count(),
            'Comp Off' => $attendanceRecords->where('status', 'Comp Off')->count(),
        ];

        return view('admin.salary.slip', compact('salaryRecord', 'attendanceBreakdown'));
    }

    public function setDefaultSalaries()
    {
        return response()->json([
            'success' => false,
            'message' => 'Please set CTC and In-Hand salary manually in employee records!'
        ]);
    }
    
    public function checkAutoGenerated()
    {
        $lastMonth = Carbon::now()->subMonth();
        $newSalaries = SalaryRecord::where('month', $lastMonth->month)
            ->where('year', $lastMonth->year)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        if ($newSalaries > 0) {
            return response()->json([
                'hasAutoGenerated' => true,
                'salaryData' => [
                    'count' => $newSalaries,
                    'month' => $lastMonth->month,
                    'year' => $lastMonth->year,
                    'month_name' => $lastMonth->format('F')
                ]
            ]);
        }
        
        return response()->json(['hasAutoGenerated' => false]);
    }
    
    private function calculateGrossFromInHand($inHand)
    {
        // Iterative approach to find gross that results in desired in-hand
        $gross = $inHand;
        
        for ($i = 0; $i < 10; $i++) {
            $basic = $gross * 0.60;
            $pfBasic = ($basic >= 15000) ? 15000 : $basic;
            $employeePf = $pfBasic * 0.12;
            
            $employeeEsic = ($gross <= 21000) ? $gross * 0.0075 : 0;
            
            $calculatedInHand = $gross - $employeePf - $employeeEsic;
            
            if (abs($calculatedInHand - $inHand) < 0.01) {
                break;
            }
            
            $gross = $gross + ($inHand - $calculatedInHand);
        }
        
        return round($gross, 2);
    }

    public function exportExcel(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        
        return Excel::download(new SalaryExport($month, $year), "Salary_{$monthName}_{$year}.xlsx");
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'salary_ids' => 'nullable|array',
            'salary_ids.*' => 'exists:salary_records,id',
            'custom_emails' => 'nullable|string'
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $salaryRecord = null;

        // Get first salary record for custom emails
        if ($request->has('salary_ids') && count($request->salary_ids) > 0) {
            $salaryRecord = SalaryRecord::with('employee')->find($request->salary_ids[0]);
        } else {
            // If no employee selected, get any salary record from current month
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $salaryRecord = SalaryRecord::with('employee')
                ->where('month', $month)
                ->where('year', $year)
                ->first();
        }

        // Send to selected employees
        if ($request->has('salary_ids')) {
            foreach ($request->salary_ids as $salaryId) {
                $record = SalaryRecord::with('employee')->find($salaryId);
                
                if ($record && $record->employee->email) {
                    try {
                        Mail::to($record->employee->email)->send(new SalarySlipMail($record));
                        $sentCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                    }
                }
            }
        }

        // Send to custom emails
        if ($request->custom_emails && $salaryRecord) {
            $customEmails = array_map('trim', explode(',', $request->custom_emails));
            $customEmails = array_filter($customEmails, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            foreach ($customEmails as $email) {
                try {
                    Mail::to($email)->send(new SalarySlipMail($salaryRecord));
                    $sentCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }
        }

        if ($sentCount == 0 && $failedCount == 0) {
            return redirect()->back()->with('error', 'Please select employees or enter custom emails');
        }

        $message = "Emails sent successfully to {$sentCount} recipient(s)";
        if ($failedCount > 0) {
            $message .= ". Failed: {$failedCount}";
        }

        return redirect()->back()->with('success', $message);
    }
}