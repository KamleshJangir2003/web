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
        return view('admin.face-attendance.index');
    }

    public function register()
    {
        $employees = Employee::all();
        return view('admin.face-attendance.register', compact('employees'));
    }

    public function saveFaceData(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'face_descriptor' => 'required|string'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $employee->face_descriptor = $request->face_descriptor;
        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Face registered successfully'
        ]);
    }

    public function getEmployeeFaceData($id)
    {
        $employee = Employee::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'descriptor' => $employee->face_descriptor
        ]);
    }

    public function getAllFaceData()
    {
        return $this->getAllFaces();
    }

    public function markAttendance(Request $request)
    {
        return $this->mark($request);
    }

    public function getAllFaces()
    {
        $employees = Employee::whereNotNull('face_descriptor')->get(['id', 'employee_id', 'name', 'face_descriptor as descriptor']);
        
        return response()->json([
            'success' => true,
            'employees' => $employees
        ]);
    }

    public function mark(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'face_descriptor' => 'required|string'
        ]);

        $employee = Employee::with('shift')->findOrFail($request->employee_id);
        $today = Carbon::today();
        $now = Carbon::now();

        // Check existing attendance
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Case 1: No attendance record - Mark Check-In
        if (!$attendance) {
            return $this->markCheckIn($employee, $today, $now);
        }

        // Case 2: Check-in exists, check-out is null - Mark Check-Out
        if ($attendance->check_in && !$attendance->check_out) {
            return $this->markCheckOut($attendance, $now);
        }

        // Case 3: Both check-in and check-out exist
        return response()->json([
            'success' => false,
            'message' => 'Attendance already completed for today'
        ]);
    }

    private function markCheckIn($employee, $today, $now)
    {
        $checkInTime = $now->format('H:i:s');
        $lateMinutes = 0;
        $status = 'Present';

        // Calculate late minutes if shift exists
        if ($employee->shift) {
            $shiftStart = Carbon::parse($employee->shift->start_time);
            $checkInCarbon = Carbon::parse($checkInTime);

            if ($checkInCarbon->gt($shiftStart)) {
                $lateMinutes = $checkInCarbon->diffInMinutes($shiftStart);
                $status = 'Late';
            }
        }

        // Half Day logic: Check-in after 12:00 PM
        if ($now->format('H:i:s') > '12:00:00') {
            $status = 'Half Day';
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in' => $checkInTime,
            'late_minutes' => $lateMinutes,
            'status' => $status,
            'shift_id' => $employee->shift_id
        ]);

        return response()->json([
            'success' => true,
            'type' => 'check_in',
            'employee_name' => $employee->name,
            'time' => $now->format('h:i A'),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'message' => 'Check-in successful'
        ]);
    }

    private function markCheckOut($attendance, $now)
    {
        $checkOutTime = $now->format('H:i:s');
        
        // Calculate working hours
        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($checkOutTime);
        $workingHours = $checkOut->diffInMinutes($checkIn) / 60;

        $attendance->update([
            'check_out' => $checkOutTime,
            'working_hours' => round($workingHours, 2)
        ]);

        return response()->json([
            'success' => true,
            'type' => 'check_out',
            'employee_name' => $attendance->employee->name,
            'time' => $now->format('h:i A'),
            'working_hours' => round($workingHours, 2),
            'message' => 'Check-out successful'
        ]);
    }
}
