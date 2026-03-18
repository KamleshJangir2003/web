<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\SalaryRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $view_type = $request->get('view_type', 'daily');
        $selected_date = $request->get('date', date('Y-m-d'));
        $selected_shift = $request->get('shift', 'Day');
        $selected_week = $request->get('week', date('Y-\WW'));
        $selected_month = $request->get('month', date('Y-m'));
        $department_filter = $request->get('department', '');
        $search_employee = $request->get('search', '');

        // Get departments for filter
        $departments = Employee::where('user_type', 'employee')
        ->where('employee_status', 'active')
        ->where('hired_status', 'hired')
        ->whereNotNull('joining_date')
        ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()')
        ->distinct()
        ->pluck('department')
        ->filter();

        // Build employee query with filters
        $query = Employee::where('user_type', 'employee')
            ->where('employee_status', 'active')
            ->where('hired_status', 'hired')
            ->whereNotNull('joining_date')
            ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()');

        if ($department_filter) {
            $query->where('department', $department_filter);
        }

        if ($search_employee) {
            $query->where(function($q) use ($search_employee) {
                $q->where('first_name', 'like', "%$search_employee%")
                  ->orWhere('last_name', 'like', "%$search_employee%")
                  ->orWhere('email', 'like', "%$search_employee%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search_employee%"]);
            });
        }

        $filtered_logs = DB::table('attendance_logs as al')
            ->leftJoin('employees as e', 'al.employee_id', '=', 'e.employee_id')
            ->select(
                'al.*',
                'e.employee_id as employee_code',
                'e.id as emp_id',
                DB::raw("CONCAT(COALESCE(e.first_name, ''), ' ', COALESCE(e.last_name, '')) as employee_name")
            )
            ->where('al.date', $selected_date)
            ->orderBy('al.date', 'desc')
            ->orderBy('al.entry_time', 'desc')
            ->get();
            
        // Debug: Check if logs are being fetched
        // dd($filtered_logs);
        // Order employees with recent attendance first, then by name
        $employees = $query->leftJoin('attendance', function($join) use ($selected_date) {
                $join->on('employees.id', '=', 'attendance.employee_id')
                     ->where('attendance.attendance_date', '=', $selected_date);
            })
            ->select('employees.*')
            ->orderBy('employees.first_name')
            ->get();
        $attendance_data = [];
        $attendance_summary = [];

        if ($view_type === 'daily') {
            // Daily view - merge data from both attendance and attendance_logs tables
            if ($employees->count() > 0) {
                $employee_ids = $employees->pluck('id')->toArray();
                
                // Get manual attendance records
                $attendance_records = DB::table('attendance')
                    ->whereIn('employee_id', $employee_ids)
                    ->where('attendance_date', $selected_date)
                    ->get();

                foreach ($attendance_records as $att) {
                    $attendance_data[$att->employee_id] = $att;
                }
                
                // Merge face recognition attendance logs
                $face_logs = DB::table('attendance_logs')
                    ->where('date', $selected_date)
                    ->get();
                
                // Debug: Log the face recognition data
                \Log::info('Face Recognition Logs:', ['count' => $face_logs->count(), 'logs' => $face_logs->toArray()]);
                \Log::info('Employees:', ['count' => $employees->count(), 'employee_ids' => $employees->pluck('employee_id')->toArray()]);
                    
                foreach ($face_logs as $log) {
                    // Find employee by employee_id (KIO03, etc)
                    $employee = $employees->firstWhere('employee_id', $log->employee_id);
                    
                    if ($employee) {
                        // If no manual attendance exists, create from face log
                        if (!isset($attendance_data[$employee->id])) {
                            $attendance_data[$employee->id] = (object)[
                                'employee_id' => $employee->id,
                                'attendance_date' => $log->date,
                                'shift_status' => ucfirst(str_replace('_', ' ', $log->shift_status)),
                                'entry_time' => $log->entry_time,
                                'exit_time' => $log->exit_time,
                                'reason' => null,
                                'from_face_recognition' => true
                            ];
                        } else {
                            // Update existing record with face log data if times are empty
                            if (empty($attendance_data[$employee->id]->entry_time)) {
                                $attendance_data[$employee->id]->entry_time = $log->entry_time;
                            }
                            if (empty($attendance_data[$employee->id]->exit_time)) {
                                $attendance_data[$employee->id]->exit_time = $log->exit_time;
                            }
                            if (empty($attendance_data[$employee->id]->shift_status)) {
                                $attendance_data[$employee->id]->shift_status = ucfirst(str_replace('_', ' ', $log->shift_status));
                            }
                        }
                    }
                }
            }
        } elseif ($view_type === 'weekly') {
            // Weekly view
            $year = substr($selected_week, 0, 4);
            $week = substr($selected_week, 6, 2);
            $start_date = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $end_date = $start_date->copy()->endOfWeek();
            
            $attendance_summary = $this->getAttendanceSummary($employees, $start_date, $end_date);
        } elseif ($view_type === 'monthly') {
            // Monthly view
            $start_date = Carbon::createFromFormat('Y-m', $selected_month)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            
            $attendance_summary = $this->getAttendanceSummary($employees, $start_date, $end_date);
        }

        return view('admin.attendance.index', compact(
            'employees', 
            'attendance_data', 
            'attendance_summary',
            'selected_date', 
            'selected_shift',
            'selected_week',
            'selected_month',
            'view_type',
            'filtered_logs',
            'departments', 
            'department_filter', 
            'search_employee'
        ));
    }

    private function getAttendanceSummary($employees, $start_date, $end_date)
    {
        $summary = [];
        
        foreach ($employees as $emp) {
            $attendance_records = DB::table('attendance')
                ->where('employee_id', $emp->id)
                ->whereBetween('attendance_date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])
                ->get();
            
            // Use shift_status field (face recognition uses this)
            $present = $attendance_records->where('shift_status', 'Present')->count();
            $absent = $attendance_records->where('shift_status', 'Absent')->count();
            $half_day = $attendance_records->where('shift_status', 'Half Day')->count();
            $paid_leave = $attendance_records->where('shift_status', 'Paid Leave')->count();
            $comp_off = $attendance_records->where('shift_status', 'Comp Off')->count();
            $unauthorized_leave = $attendance_records->where('shift_status', 'Unauthorized Leave')->count();
            $holiday = $attendance_records->where('shift_status', 'Holiday')->count();
            $week_off = $attendance_records->where('shift_status', 'Week Off')->count();
            
            // Calculate total excluding Absent and Unauthorized Leave
            $total = $present + ($half_day * 0.5) + $paid_leave + $comp_off + $holiday + $week_off;
            
            $summary[] = [
                'name' => $emp->first_name . ' ' . $emp->last_name,
                'department' => $emp->department ?? 'N/A',
                'present' => $present,
                'absent' => $absent,
                'half_day' => $half_day,
                'paid_leave' => $paid_leave,
                'comp_off' => $comp_off,
                'unauthorized_leave' => $unauthorized_leave,
                'holiday' => $holiday,
                'week_off' => $week_off,
                'total' => $total
            ];
        }
        
        return $summary;
    }

    private function checkAndGenerateSalary($attendance_date)
    {
        $date = Carbon::parse($attendance_date);
        
        // Check if it's 22nd (payroll cycle end: 23rd to 22nd)
        if ($date->day == 22) {
            $month = $date->month;
            $year = $date->year;
            
            // Check if salary already generated for this payroll cycle
            $existingSalaries = SalaryRecord::where('month', $month)
                ->where('year', $year)
                ->count();
                
            if ($existingSalaries == 0) {
                // Generate salary for all employees
                $generatedCount = $this->generateMonthlySalary($month, $year);
                
                if ($generatedCount > 0) {
                    session()->flash('salary_generated', [
                        'count' => $generatedCount,
                        'month' => $month,
                        'year' => $year,
                        'month_name' => Carbon::create($year, $month)->format('F')
                    ]);
                }
            }
        }
    }
    
    private function generateMonthlySalary($month, $year)
    {
        $employees = Employee::where('user_type', 'employee')
        ->where('employee_status', 'active')
        ->where('hired_status', 'hired')
        ->whereNotNull('joining_date')
        ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()')
        ->whereNotNull('in_hand_salary')
        ->where('in_hand_salary', '>', 0)
        ->get();

        // Payroll cycle: 23rd previous month to 22nd current month
        $startDate = Carbon::create($year, $month, 23)->subMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 22)->format('Y-m-d');
        $totalDaysInCycle = 30; // Fixed 30 days cycle (23 to 22)
        $generatedCount = 0;

        foreach ($employees as $employee) {
            $existingRecord = SalaryRecord::where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();
                
            if ($existingRecord) {
                continue;
            }

            $inHandSalary = $employee->in_hand_salary;
            
            $gross = $this->calculateGrossFromInHand($inHandSalary);
            $basic = $gross * 0.60;
            $hra = $gross * 0.40;
            
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

            // Get attendance from 23rd previous month to 22nd current month
            $attendanceRecords = DB::table('attendance')
                ->where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            $present = $attendanceRecords->where('shift_status', 'Present')->count();
            $late = $attendanceRecords->where('shift_status', 'Late')->count();
            $absent = $attendanceRecords->where('shift_status', 'Absent')->count();
            $halfDay = $attendanceRecords->where('shift_status', 'Half Day')->count();
            $unauthorizedLeave = $attendanceRecords->where('shift_status', 'Unauthorized Leave')->count();
            $paidLeave = $attendanceRecords->where('shift_status', 'Paid Leave')->count();
            $holiday = $attendanceRecords->where('shift_status', 'Holiday')->count();
            $weekOff = $attendanceRecords->where('shift_status', 'Week Off')->count();
            $compOff = $attendanceRecords->where('shift_status', 'Comp Off')->count();

            // Calculate working days: Present + Late (both count as full day) + Half Day (0.5) + Paid Leave + Comp Off + Holiday + Week Off
            $workingDays = $present + $late + ($halfDay * 0.5) + $paidLeave + $compOff + $holiday + $weekOff;

            // Per day salary based on 30-day cycle
            $perDaySalary = $inHandSalary / $totalDaysInCycle;

            $earnedSalary = $workingDays * $perDaySalary;
            $deduction = $inHandSalary - $earnedSalary;
            $netSalary = $earnedSalary - $employeePf - $employeeEsic;

            SalaryRecord::create([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'basic_salary' => $inHandSalary,
                'working_days' => $workingDays,
                'deduction' => $deduction,
                'advance' => 0,
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
        
        return $generatedCount;
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

    public function store(Request $request)
    {
        $attendance_date = $request->date;
        $shift = $request->shift ?? 'Day';
        
        foreach ($request->employees as $employee_id => $data) {
            $status = $data['shift_status'] ?? '';
            
            // Skip if no status selected
            if (empty($status)) {
                continue;
            }
            
            $entry_time = !empty($data['entry_time']) ? $data['entry_time'] : null;
            $exit_time = !empty($data['exit_time']) ? $data['exit_time'] : null;
            $reason = $data['reason'] ?? null;
            
            // Get employee details
            $employee = Employee::find($employee_id);
            
            // Save to attendance table (manual)
            Attendance::updateOrCreate(
                [
                    'employee_id' => $employee_id,
                    'attendance_date' => $attendance_date
                ],
                [
                    'shift' => $shift,
                    'shift_status' => $status,
                    'entry_time' => $entry_time,
                    'exit_time' => $exit_time,
                    'reason' => $reason
                ]
            );
            
            // Also sync to attendance_logs table if employee_id exists
            if ($employee && $employee->employee_id) {
                DB::table('attendance_logs')->updateOrInsert(
                    [
                        'employee_id' => $employee->employee_id,
                        'date' => $attendance_date
                    ],
                    [
                        'shift_type' => $shift,
                        'shift_status' => strtolower($status),
                        'entry_time' => $entry_time,
                        'exit_time' => $exit_time,
                        'overtime_minutes' => 0,
                        'overtime_hours' => 0.00
                    ]
                );
            }
            
            // Update salary record if already generated for this month
            $this->updateSalaryIfExists($employee_id, $attendance_date);
        }
        
        // Check if month is complete and auto-generate salary
        $this->checkAndGenerateSalary($attendance_date);
        
        $message = 'Attendance saved successfully!';
        
        // Check if salary was generated
        if (session()->has('salary_generated')) {
            $salaryData = session('salary_generated');
            $message .= " Additionally, salary for {$salaryData['count']} employees has been automatically generated for {$salaryData['month_name']} {$salaryData['year']}. Check the salary page for details.";
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    private function updateSalaryIfExists($employee_id, $attendance_date)
    {
        $date = Carbon::parse($attendance_date);
        $month = $date->month;
        $year = $date->year;
        
        // Check if salary record exists for this month
        $salaryRecord = SalaryRecord::where('employee_id', $employee_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
            
        if (!$salaryRecord) {
            return; // No salary record to update
        }
        
        $employee = Employee::find($employee_id);
        if (!$employee || !$employee->in_hand_salary) {
            return;
        }
        
        // Recalculate based on current attendance
        $attendanceRecords = Attendance::where('employee_id', $employee_id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();
            
        $present = $attendanceRecords->where('shift_status', 'Present')->count();
        $halfDay = $attendanceRecords->where('shift_status', 'Half Day')->count();
        $paidLeave = $attendanceRecords->where('shift_status', 'Paid Leave')->count();
        $holiday = $attendanceRecords->where('shift_status', 'Holiday')->count();
        $weekOff = $attendanceRecords->where('shift_status', 'Week Off')->count();
        $compOff = $attendanceRecords->where('shift_status', 'Comp Off')->count();
        
        $workingDays = $present + $paidLeave + $compOff + $weekOff + $holiday + ($halfDay * 0.5);
        
        $totalDaysInMonth = Carbon::create($year, $month)->daysInMonth;
        $inHandSalary = $employee->in_hand_salary;
        $perDaySalary = $inHandSalary / $totalDaysInMonth;
        $earnedSalary = $workingDays * $perDaySalary;
        
        // Calculate PF/ESI
        $gross = $this->calculateGrossFromInHand($inHandSalary);
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
        
        // Update salary record
        $salaryRecord->update([
            'working_days' => $workingDays,
            'deduction' => $deduction,
            'employee_pf' => $employeePf,
            'employee_esi' => $employeeEsic,
            'employer_pf' => $employerPf,
            'employer_esi' => $employerEsic,
            'net_salary' => $netSalary,
        ]);
    }
}