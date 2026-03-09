# Face Recognition Attendance System - Implementation Guide

## Features Added

### 1. ✅ Check-In / Check-Out Logic
- First face scan = CHECK-IN
- Second face scan = CHECK-OUT
- Third attempt = "Attendance already completed today"

### 2. ✅ Shift Management
- **Day Shift**: 09:30 AM - 06:30 PM
- **Night Shift**: 07:30 PM - 05:10 AM (next day)
- **Custom Shift**: Admin can create custom shifts

### 3. ✅ Late Attendance Calculation
- Automatically calculates late minutes
- Stores in `late_minutes` column
- Status changes to "Late" if check-in is after shift start time

### 4. ✅ Night Shift Handling
- Attendance date belongs to shift start date
- Example: Check-in Mar 9 (7:30 PM) + Check-out Mar 10 (5:10 AM) = Attendance date: Mar 9

---

## Installation Steps

### Step 1: Run Migrations

```bash
cd cms
php artisan migrate
```

This will:
- Create `shift_types` table
- Add `shift_id` to `employees` table
- Add `check_in`, `check_out`, `date`, `late_minutes` to `attendance` table
- Insert default Day and Night shifts

### Step 2: Add Routes

Add these routes to `routes/web.php`:

```php
use App\Http\Controllers\Admin\FaceAttendanceController;
use App\Http\Controllers\Admin\ShiftTypeController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Face Attendance
    Route::get('/face-attendance', [FaceAttendanceController::class, 'index']);
    Route::post('/face-attendance/mark', [FaceAttendanceController::class, 'markAttendance']);
    Route::get('/face-attendance/all-faces', [FaceAttendanceController::class, 'getAllFaceData']);
    
    // Shift Management
    Route::resource('shifts', ShiftTypeController::class);
});
```

### Step 3: Assign Shifts to Employees

You can assign shifts in two ways:

**Option A: Via Database**
```sql
UPDATE employees SET shift_id = 1 WHERE id = 1; -- Day Shift
UPDATE employees SET shift_id = 2 WHERE id = 2; -- Night Shift
```

**Option B: Via Admin Panel**
- Go to `/admin/shifts`
- Create/Edit shifts
- Assign shift_id to employees in employee management

---

## Database Schema

### shift_types
```
id | shift_name | start_time | end_time | late_after
1  | Day Shift  | 09:30:00   | 18:30:00 | 0
2  | Night Shift| 19:30:00   | 05:10:00 | 0
```

### employees (new column)
```
shift_id (foreign key to shift_types)
```

### attendance (new columns)
```
check_in (datetime)
check_out (datetime)
date (date)
late_minutes (integer)
```

---

## How It Works

### Check-In Flow
1. Employee scans face
2. System detects face and matches with database
3. System checks if attendance exists for today
4. If NO → Create attendance with check_in time
5. Calculate late minutes based on shift start time
6. Set status = "Late" if late_minutes > 0

### Check-Out Flow
1. Employee scans face again
2. System finds today's attendance
3. If check_out is NULL → Update check_out time
4. If check_out already exists → Return "Attendance already completed"

### Late Calculation
```php
Shift Start: 09:30 AM
Check-in: 09:45 AM
Late Minutes: 15
Status: Late
```

### Night Shift Logic
```php
Shift: 07:30 PM - 05:10 AM
Check-in: Mar 9, 7:30 PM → Attendance Date: Mar 9
Check-out: Mar 10, 5:10 AM → Attendance Date: Mar 9 (same)
```

---

## API Response Examples

### Check-In Response
```json
{
  "success": true,
  "type": "check_in",
  "message": "Check-in successful",
  "employee_name": "John Doe",
  "time": "09:45 AM",
  "status": "Late",
  "late_minutes": 15
}
```

### Check-Out Response
```json
{
  "success": true,
  "type": "check_out",
  "message": "Check-out successful",
  "employee_name": "John Doe",
  "time": "06:30 PM"
}
```

### Already Completed Response
```json
{
  "success": false,
  "message": "Attendance already completed today"
}
```

---

## Testing

### Test Case 1: Normal Check-In (On Time)
1. Set employee shift_id = 1 (Day Shift: 09:30 AM)
2. Scan face at 09:25 AM
3. Expected: Status = "Present", late_minutes = 0

### Test Case 2: Late Check-In
1. Set employee shift_id = 1
2. Scan face at 09:45 AM
3. Expected: Status = "Late", late_minutes = 15

### Test Case 3: Check-Out
1. After check-in, scan face again
2. Expected: check_out time updated

### Test Case 4: Already Completed
1. After check-in and check-out, scan face again
2. Expected: "Attendance already completed today"

### Test Case 5: Night Shift
1. Set employee shift_id = 2 (Night Shift: 07:30 PM - 05:10 AM)
2. Check-in at Mar 9, 7:30 PM
3. Check-out at Mar 10, 5:10 AM
4. Expected: Both records have date = Mar 9

---

## Files Modified

1. ✅ `app/Http/Controllers/Admin/FaceAttendanceController.php` - Updated markAttendance()
2. ✅ `app/Models/Attendance.php` - Added new fields
3. ✅ `app/Models/Employee.php` - Added shiftType relationship
4. ✅ `resources/views/admin/face-attendance/index.blade.php` - Updated UI

## Files Created

1. ✅ `app/Models/ShiftType.php`
2. ✅ `app/Http/Controllers/Admin/ShiftTypeController.php`
3. ✅ `database/migrations/2024_03_10_000001_create_shift_types_table.php`
4. ✅ `database/migrations/2024_03_10_000002_add_shift_and_late_columns.php`
5. ✅ `resources/views/admin/shifts/index.blade.php`

---

## Important Notes

⚠️ **Before running migrations**, backup your database!

⚠️ **Existing functionality preserved** - Old attendance records will continue to work

⚠️ **Assign shifts to employees** - Employees without shift_id will get error message

---

## Support

If you encounter any issues:
1. Check if migrations ran successfully
2. Verify shift_id is assigned to employees
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure face_data exists for employees

---

## Future Enhancements (Optional)

- [ ] Overtime calculation
- [ ] Break time tracking
- [ ] Geolocation verification
- [ ] Email notifications for late attendance
- [ ] Monthly attendance reports
- [ ] Shift swap requests
