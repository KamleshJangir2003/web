<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FaceAttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');
        $todayAttendance = Attendance::with('employee')
            ->where('attendance_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.face-attendance.index', compact('todayAttendance'));
    }

    public function register()
    {
        $employees = Employee::where('hired_status', 'hired')->get();
    
        return view('admin.face-attendance.register', compact('employees'));
    }

    public function saveFaceData(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'face_descriptor' => 'required'
            ]);

            $employee = Employee::findOrFail($request->employee_id);
            $employee->face_data = is_string($request->face_descriptor) 
                ? $request->face_descriptor 
                : json_encode($request->face_descriptor);
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

            $employee = Employee::with('shift')->findOrFail($request->employee_id);
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
        elseif ($employee->shift && is_object($employee->shift) && isset($employee->shift->start_time)) {
            $shiftStart = Carbon::createFromFormat('H:i:s', $employee->shift->start_time);
            $checkInCarbon = Carbon::createFromFormat('H:i:s', $checkInTime);
        
            if ($checkInCarbon->gt($shiftStart)) {
                $lateMinutes = $checkInCarbon->diffInMinutes($shiftStart);
                // If late by more than 10 minutes - Mark as Half Day
                if ($lateMinutes > 10) {
                    $status = 'Half Day';
                } else {
                    $status = 'Present';
                }
            }
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
            'in_time' => $checkInTime,
            'status' => $status,
            'shift' => ($employee->shift && isset($employee->shift->name)) ? $employee->shift->name : 'Day',
            'shift_id' => $employee->shift_id
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
        $employee = Employee::with('shift')->find($attendance->employee_id);
    
        $checkOutTime = $now->format('H:i:s');
    
        // Calculate working hours
        $checkIn = Carbon::parse($attendance->in_time);
        $checkOut = Carbon::parse($checkOutTime);
        $workingHours = $checkOut->diffInMinutes($checkIn) / 60;
    
        // Calculate early checkout and overtime
        $earlyCheckoutMinutes = 0;
        $overtimeHours = 0;
        
        if ($employee->shift && isset($employee->shift->end_time)) {
            $shiftEnd = Carbon::createFromFormat('H:i:s', $employee->shift->end_time);
            $shiftStart = Carbon::createFromFormat('H:i:s', $employee->shift->start_time);
            $checkOutCarbon = Carbon::createFromFormat('H:i:s', $checkOutTime);
            
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
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'overtime_hours' => $overtimeHours
        ]);
    }
}
