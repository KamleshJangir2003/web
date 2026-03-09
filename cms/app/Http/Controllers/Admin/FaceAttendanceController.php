<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FaceAttendanceController extends Controller
{
    public function index()
    {
        $employees = Employee::where('user_type', 'employee')
            ->where('employee_status', 'active')
            ->where('hired_status', 'hired')
            ->whereNotNull('joining_date')
            ->whereRaw('DATE_ADD(joining_date, INTERVAL certification_period DAY) <= CURDATE()')
            ->get();

        return view('admin.face-attendance.index', compact('employees'));
    }

    public function register()
    {
        $employees = Employee::where('user_type', 'employee')
            ->where('employee_status', 'active')
            ->where('hired_status', 'hired')
            ->get();

        return view('admin.face-attendance.register', compact('employees'));
    }

    public function saveFaceData(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'face_descriptor' => 'required|string'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $employee->face_data = $request->face_descriptor;
        $employee->save();

        return response()->json(['success' => true, 'message' => 'Face registered successfully']);
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'face_descriptor' => 'required|string'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        if (!$employee->face_data) {
            return response()->json(['success' => false, 'message' => 'Face not registered. Please register first.'], 400);
        }

        $today = Carbon::today()->format('Y-m-d');
        $shift = $employee->shift ?? 'Day';

        // Check if attendance already marked
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->where('shift', $shift)
            ->first();

        if ($existingAttendance) {
            return response()->json(['success' => false, 'message' => 'Attendance already marked for today'], 400);
        }

        // Create attendance record
        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
            'status' => 'Present',
            'shift' => $shift,
            'in_time' => Carbon::now()->format('H:i:s'),
            'reason' => 'Face Recognition Attendance'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Attendance marked successfully',
            'employee_name' => $employee->full_name,
            'time' => Carbon::now()->format('h:i A')
        ]);
    }

    public function getEmployeeFaceData($id)
    {
        $employee = Employee::findOrFail($id);
        
        if (!$employee->face_data) {
            return response()->json(['success' => false, 'message' => 'Face data not found'], 404);
        }

        return response()->json([
            'success' => true,
            'face_data' => $employee->face_data,
            'employee_name' => $employee->full_name
        ]);
    }

    public function getAllFaceData()
    {
        $employees = Employee::where('user_type', 'employee')
            ->where('employee_status', 'active')
            ->where('hired_status', 'hired')
            ->whereNotNull('face_data')
            ->get(['id', 'face_data', 'first_name', 'last_name', 'employee_id']);

        $faceData = $employees->map(function($emp) {
            return [
                'id' => $emp->id,
                'employee_id' => $emp->employee_id,
                'name' => $emp->first_name . ' ' . $emp->last_name,
                'descriptor' => $emp->face_data
            ];
        });

        return response()->json(['success' => true, 'employees' => $faceData]);
    }
}
