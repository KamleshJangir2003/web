# TROUBLESHOOTING: "Face detected. Matching..." Stuck Issue

## Problem
System gets stuck at "Face detected. Matching..." and doesn't proceed.

## Root Causes & Solutions

### 1. Database Not Set Up
**Check**: Run migrations
```bash
cd cms
php artisan migrate
```

**Verify**:
```sql
-- Check if shift_types table exists
SHOW TABLES LIKE 'shift_types';

-- Check if columns exist
SHOW COLUMNS FROM employees LIKE 'shift_id';
SHOW COLUMNS FROM attendance LIKE 'check_in';
```

---

### 2. No Shifts Assigned to Employees
**Quick Fix**: Run this SQL
```sql
UPDATE employees 
SET shift_id = 1 
WHERE employee_status = 'active' 
  AND hired_status = 'hired'
  AND shift_id IS NULL;
```

**Verify**:
```sql
SELECT id, employee_id, full_name, shift_id 
FROM employees 
WHERE face_data IS NOT NULL;
```

---

### 3. ShiftType Model Missing
**Check**: File exists at `cms/app/Models/ShiftType.php`

If missing, create it with this content:
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShiftType extends Model
{
    protected $fillable = ['shift_name', 'start_time', 'end_time', 'late_after'];
    public function employees() {
        return $this->hasMany(Employee::class, 'shift_id');
    }
}
```

---

### 4. Check Browser Console
**Steps**:
1. Open browser DevTools (F12)
2. Go to Console tab
3. Click "Mark Attendance"
4. Look for errors

**Common Errors**:
- `500 Internal Server Error` → Check Laravel logs
- `CSRF token mismatch` → Refresh page
- `Column not found` → Run migrations

---

### 5. Check Laravel Logs
**Location**: `cms/storage/logs/laravel.log`

**Command**:
```bash
tail -f cms/storage/logs/laravel.log
```

**Common Errors**:
- `Class 'ShiftType' not found` → Model missing
- `Column 'shift_id' not found` → Migration not run
- `Call to undefined method` → Relationship missing

---

### 6. Clear Cache
```bash
cd cms
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### 7. Test API Directly
**Using Browser Console**:
```javascript
// Test get all faces
fetch('/admin/face-attendance/all-faces')
  .then(r => r.json())
  .then(d => console.log(d));

// Test mark attendance
fetch('/admin/face-attendance/mark', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    employee_id: 1,
    face_descriptor: '[]'
  })
})
.then(r => r.json())
.then(d => console.log(d))
.catch(e => console.error(e));
```

---

### 8. Check Route Registration
**File**: `cms/routes/web.php`

**Should have**:
```php
use App\Http\Controllers\Admin\FaceAttendanceController;

Route::prefix('admin')->group(function () {
    Route::post('/face-attendance/mark', [FaceAttendanceController::class, 'markAttendance']);
    Route::get('/face-attendance/all-faces', [FaceAttendanceController::class, 'getAllFaceData']);
});
```

**Verify**:
```bash
php artisan route:list | grep face-attendance
```

---

### 9. Database Connection
**Check**: `cms/.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Test**:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

---

### 10. Complete Reset (Last Resort)
```bash
# Backup first!
php artisan migrate:fresh
php artisan migrate
```

Then run:
```sql
INSERT INTO shift_types (shift_name, start_time, end_time, late_after) VALUES
('Day Shift', '09:30:00', '18:30:00', 0),
('Night Shift', '19:30:00', '05:10:00', 0);

UPDATE employees SET shift_id = 1 WHERE shift_id IS NULL;
```

---

## Quick Checklist

- [ ] Migrations run successfully
- [ ] shift_types table has 2 records
- [ ] Employees have shift_id assigned
- [ ] ShiftType.php model exists
- [ ] Employee model has shiftType() relationship
- [ ] shift_id in Employee fillable array
- [ ] Browser console shows no errors
- [ ] Laravel logs show no errors
- [ ] Routes registered correctly
- [ ] Cache cleared

---

## Still Not Working?

### Enable Debug Mode
**File**: `cms/.env`
```
APP_DEBUG=true
```

### Check Response in Network Tab
1. Open DevTools (F12)
2. Go to Network tab
3. Click "Mark Attendance"
4. Find the `/mark` request
5. Check Response tab for error details

### Get Help
Share these details:
1. Browser console errors
2. Laravel log errors
3. Network response
4. Database structure (SHOW CREATE TABLE employees)

---

## Success Indicators

✅ No errors in browser console
✅ No errors in Laravel logs
✅ API returns JSON response
✅ Attendance record created in database
✅ Frontend shows success message

---

Run `debug_setup.sql` to check everything at once!
