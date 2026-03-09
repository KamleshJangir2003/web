<?php

// Add this to routes/web.php temporarily for testing

Route::get('/test-face-setup', function() {
    try {
        // Test 1: Check ShiftType model
        $shifts = \App\Models\ShiftType::all();
        
        // Test 2: Check Employee with shift
        $employee = \App\Models\Employee::with('shiftType')
            ->where('face_data', '!=', null)
            ->first();
        
        // Test 3: Check Attendance model
        $attendance = \App\Models\Attendance::latest()->first();
        
        return response()->json([
            'status' => 'OK',
            'shifts_count' => $shifts->count(),
            'shifts' => $shifts,
            'test_employee' => [
                'id' => $employee->id ?? 'N/A',
                'name' => $employee->full_name ?? 'N/A',
                'shift_id' => $employee->shift_id ?? 'N/A',
                'has_shift' => $employee->shiftType ? 'YES' : 'NO',
                'shift_name' => $employee->shiftType->shift_name ?? 'N/A'
            ],
            'latest_attendance' => $attendance ? [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'date' => $attendance->date,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out
            ] : 'No attendance records'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});
