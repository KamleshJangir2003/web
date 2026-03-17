<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FaceAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        // Cache today's attendance snapshot briefly to reduce repeated hits
        $todayAttendance = Cache::remember("face_attendance:today:{$today}", 30, function () use ($today) {
            return Attendance::with('employee')
                ->where('attendance_date', $today)
                ->get();
        });
        
        return view('admin.face-attendance.index', compact('todayAttendance'));
    }

    /**
     * Return today's attendance logs from the attendance_logs table
     * in the format expected by the frontend.
     */
    public function todayLogs()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Assuming attendance_logs has compatible columns with the API response
        $logs = DB::table('attendance_logs')
            ->whereDate('date', $today)
            ->orderBy('entry_time')
            ->get([
                'employee_id',
                'date',
                'entry_time',
                'exit_time',
                'shift_type',
                'shift_status',
                'total_work_time',
                'overtime_minutes',
                'overtime_hours',
            ]);

        return response()->json($logs);
    }

    public function register()
    {
        $employees = DB::table('employees')
            ->leftJoin('face_registrations', 'employees.employee_id', '=', 'face_registrations.employee_id')
            ->select(
                'employees.employee_id',
                DB::raw("CONCAT(employees.first_name,' ',employees.last_name) as full_name"),
                'face_registrations.face_encoding'
            )
            ->where('employees.employee_status', 'active')
            ->where('employees.hired_status', 'hired')
            ->whereDate('employees.joining_date', '<=', now()->subDays(5))
            ->get();

        return view('admin.face-attendance.register', [
            'employees' => $employees
        ]);
    }

    public function saveFaceData(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'face_descriptor' => 'required'
            ]);

            $employee = Employee::findOrFail($request->employee_id);
            $newDescriptor = is_string($request->face_descriptor) 
                ? json_decode($request->face_descriptor, true)
                : $request->face_descriptor;

            // Check for duplicate faces
            $duplicateFace = $this->findDuplicateFace($newDescriptor, $employee->id);
            if ($duplicateFace) {
                return response()->json([
                    'success' => false,
                    'message' => 'This face is already registered with another employee',
                    'duplicate_employee' => $duplicateFace['name']
                ], 409);
            }

            $employee->face_data = json_encode($newDescriptor);
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Face registered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeeFaceData($id)
    {
        $employee = Employee::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'descriptor' => $employee->face_data
        ]);
    }

    public function getAllFaceData()
    {
        try {
            \Log::info('getAllFaceData called');
            $employees = Employee::whereNotNull('face_data')
                ->where('face_data', '!=', '')
                ->where('employee_status', 'active')
                ->get(['id', 'employee_id', 'first_name', 'last_name', 'face_data'])
                ->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'employee_id' => $emp->employee_id,
                        'name' => $emp->first_name . ' ' . $emp->last_name,
                        'descriptor' => $emp->face_data
                    ];
                });
            
            \Log::info('Found employees: ' . $employees->count());
            
            return response()->json([
                'success' => true,
                'employees' => $employees
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('getAllFaceData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function markAttendance(Request $request)
    {
        return $this->mark($request);
    }

    public function getAllFaces()
    {
        try {
            $employees = Employee::whereNotNull('face_data')
                ->where('face_data', '!=', '')
                ->where('employee_status', 'active')
                ->get(['id', 'employee_id', 'first_name', 'last_name', 'face_data'])
                ->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'employee_id' => $emp->employee_id,
                        'name' => $emp->first_name . ' ' . $emp->last_name,
                        'descriptor' => $emp->face_data
                    ];
                });
            
            return response()->json([
                'success' => true,
                'employees' => $employees
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function mark(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'face_descriptor' => 'required|string'
            ]);

            $employee = Employee::with('shiftType')->findOrFail($request->employee_id);
            $today = Carbon::today()->format('Y-m-d');
            $now = Carbon::now();

            // Verify face descriptor matches stored face
            $detectionDescriptor = json_decode($request->face_descriptor, true);
            $storedDescriptor = json_decode($employee->face_data, true);
            
            if (!$storedDescriptor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee face not registered'
                ], 400);
            }
            
            // Calculate euclidean distance
            $distance = $this->euclideanDistance($detectionDescriptor, $storedDescriptor);
            
            // Strict threshold: 0.5 (stricter than frontend 0.65)
            if ($distance > 0.5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face verification failed. Distance too high: ' . round($distance, 3)
                ], 403);
            }

            // Check existing attendance
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', $today)
                ->first();

            // Case 1: No attendance record - Mark Check-In
            if (!$attendance) {
                return $this->markCheckIn($employee, $today, $now);
            }

            // Case 2: Check-in exists, check-out is null - Mark Check-Out
            if ($attendance->in_time && !$attendance->out_time) {
                return $this->markCheckOut($attendance, $now);
            }

            // Case 3: Both check-in and check-out exist
            return response()->json([
                'success' => false,
                'message' => 'Attendance already completed for today'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function euclideanDistance($arr1, $arr2)
    {
        $sum = 0;
        for ($i = 0; $i < count($arr1); $i++) {
            $diff = $arr1[$i] - $arr2[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    private function findDuplicateFace($newDescriptor, $currentEmployeeId)
    {
        $threshold = 0.45;
        $employees = Employee::whereNotNull('face_data')
            ->where('face_data', '!=', '')
            ->where('id', '!=', $currentEmployeeId)
            ->get(['id', 'first_name', 'last_name', 'face_data']);

        foreach ($employees as $employee) {
            $storedDescriptor = json_decode($employee->face_data, true);
            if (!is_array($storedDescriptor)) {
                continue;
            }

            if (count($newDescriptor) !== count($storedDescriptor)) {
                continue;
            }

            $distance = $this->euclideanDistance($newDescriptor, $storedDescriptor);
            if ($distance < $threshold) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'distance' => $distance
                ];
            }
        }

        return null;
    }

    private function markCheckIn($employee, $today, $now)
    {
        $checkInTime = $now->format('H:i:s');
        $lateMinutes = 0;
        $status = 'Present';

        // Check if Sunday - Mark as Week Off (paid)
        if ($now->dayOfWeek === Carbon::SUNDAY) {
            $status = 'Week Off';
        }
        // Calculate late minutes if shift exists
        elseif ($employee->shift_id && $employee->shiftType) {
            $shiftStart = $employee->shiftType->start_time;
            
            if ($shiftStart) {
                try {
                    $shiftStartTime = Carbon::createFromFormat('H:i:s', $shiftStart);
                    $checkInCarbon = Carbon::createFromFormat('H:i:s', $checkInTime);
                
                    if ($checkInCarbon->gt($shiftStartTime)) {
                        // Calculate late minutes: check-in time - shift start time (always positive)
                        $lateMinutes = abs($checkInCarbon->diffInMinutes($shiftStartTime));
                        
                        // Determine status based on late minutes
                        if ($lateMinutes <= 15) {
                            $status = 'Present'; // Grace time
                        } elseif ($lateMinutes <= 120) {
                            $status = 'Late';
                        } elseif ($lateMinutes <= 240) {
                            $status = 'Half Day';
                        } else {
                            $status = 'Absent';
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Time parsing error: ' . $e->getMessage());
                }
            }
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
            'in_time' => $checkInTime,
            'status' => $status,
            'shift' => ($employee->shiftType && $employee->shiftType->shift_name) ? $employee->shiftType->shift_name : 'Day Shift',
            'shift_id' => $employee->shift_id,
            'late_minutes' => $lateMinutes,
            'early_checkout_minutes' => 0,
            'overtime_hours' => 0
        ]);

        return response()->json([
            'success' => true,
            'type' => 'check_in',
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'time' => $checkInTime,
            'status' => $status,
            'late_minutes' => $lateMinutes
        ]);
    }

    private function markCheckOut($attendance, $now)
    {
        $employee = Employee::with('shiftType')->find($attendance->employee_id);
    
        $checkOutTime = $now->format('H:i:s');
    
        // Calculate working hours
        $checkIn = Carbon::parse($attendance->in_time);
        $checkOut = Carbon::parse($checkOutTime);
        $workingHours = $checkOut->diffInMinutes($checkIn) / 60;
    
        // Calculate early checkout and overtime
        $earlyCheckoutMinutes = 0;
        $overtimeHours = 0;
        
        if ($employee->shiftType && $employee->shiftType->end_time) {
            $shiftEnd = Carbon::createFromFormat('H:i:s', $employee->shiftType->end_time);
            $shiftStart = Carbon::createFromFormat('H:i:s', $employee->shiftType->start_time);
            $checkOutCarbon = Carbon::createFromFormat('H:i:s', $checkOutTime);
            
            // Handle night shift (end time < start time means shift ends next day)
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
                $checkOutCarbon->addDay();
            }
            
            // Check for early checkout
            if ($checkOutCarbon->lt($shiftEnd)) {
                $earlyCheckoutMinutes = $shiftEnd->diffInMinutes($checkOutCarbon);
            }
            // Check for overtime
            elseif ($checkOutCarbon->gt($shiftEnd)) {
                $overtimeMinutes = $checkOutCarbon->diffInMinutes($shiftEnd);
                $overtimeHours = round($overtimeMinutes / 60, 2);
            }
        }
    
        $attendance->update([
            'out_time' => $checkOutTime,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'overtime_hours' => $overtimeHours
        ]);
    
        return response()->json([
            'success' => true,
            'type' => 'check_out',
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'time' => $checkOutTime,
            'working_hours' => round($workingHours, 2),
            'late_minutes' => $attendance->late_minutes,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'overtime_hours' => $overtimeHours
        ]);
    }
}
