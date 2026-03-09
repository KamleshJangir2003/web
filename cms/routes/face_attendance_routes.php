// Add these routes to your web.php file

// Face Attendance Routes (existing + updated)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/face-attendance', [FaceAttendanceController::class, 'index'])->name('face-attendance.index');
    Route::get('/face-attendance/register', [FaceAttendanceController::class, 'register'])->name('face-attendance.register');
    Route::post('/face-attendance/save-face', [FaceAttendanceController::class, 'saveFaceData'])->name('face-attendance.save');
    Route::post('/face-attendance/mark', [FaceAttendanceController::class, 'markAttendance'])->name('face-attendance.mark');
    Route::get('/face-attendance/all-faces', [FaceAttendanceController::class, 'getAllFaceData'])->name('face-attendance.all-faces');
    
    // Shift Management Routes (NEW)
    Route::resource('shifts', ShiftTypeController::class);
});
