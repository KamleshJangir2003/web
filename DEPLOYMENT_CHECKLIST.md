# 📋 DEPLOYMENT CHECKLIST

## Pre-Deployment

- [ ] Backup current database
- [ ] Backup current codebase
- [ ] Review all modified files
- [ ] Test in development environment first

---

## Step 1: Database Setup

- [ ] Run migration: `php artisan migrate`
- [ ] Verify `shift_types` table created
- [ ] Verify Day Shift and Night Shift inserted
- [ ] Verify `employees.shift_id` column added
- [ ] Verify `attendance` columns added (check_in, check_out, date, late_minutes)
- [ ] Check foreign key constraint created

**Verification Query**:
```sql
SHOW TABLES LIKE 'shift_types';
DESCRIBE employees;
DESCRIBE attendance;
SELECT * FROM shift_types;
```

---

## Step 2: Code Deployment

### Modified Files
- [ ] Copy `FaceAttendanceController.php` to `cms/app/Http/Controllers/Admin/`
- [ ] Copy `Attendance.php` to `cms/app/Models/`
- [ ] Copy `Employee.php` to `cms/app/Models/`
- [ ] Copy `index.blade.php` to `cms/resources/views/admin/face-attendance/`

### New Files
- [ ] Copy `ShiftType.php` to `cms/app/Models/`
- [ ] Copy `ShiftTypeController.php` to `cms/app/Http/Controllers/Admin/`
- [ ] Copy `index.blade.php` to `cms/resources/views/admin/shifts/`

---

## Step 3: Routes Configuration

- [ ] Open `cms/routes/web.php`
- [ ] Add ShiftTypeController import
- [ ] Add shift management routes
- [ ] Test route: `php artisan route:list | grep shift`

**Add to web.php**:
```php
use App\Http\Controllers\Admin\ShiftTypeController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('shifts', ShiftTypeController::class);
});
```

---

## Step 4: Assign Shifts to Employees

- [ ] Identify employees for Day Shift
- [ ] Identify employees for Night Shift
- [ ] Run SQL update queries
- [ ] Verify shift assignments

**SQL Commands**:
```sql
-- Assign Day Shift
UPDATE employees SET shift_id = 1 WHERE id IN (1,2,3);

-- Assign Night Shift
UPDATE employees SET shift_id = 2 WHERE id IN (4,5,6);

-- Verify
SELECT id, employee_id, full_name, shift_id FROM employees WHERE shift_id IS NOT NULL;
```

---

## Step 5: Testing

### Test 1: Check-In (On Time)
- [ ] Login as admin
- [ ] Go to `/admin/face-attendance`
- [ ] Start camera
- [ ] Scan employee face (before shift start time)
- [ ] Verify: Status = "Present", late_minutes = 0
- [ ] Check database record

### Test 2: Check-In (Late)
- [ ] Scan employee face (after shift start time)
- [ ] Verify: Status = "Late", late_minutes > 0
- [ ] Check database record

### Test 3: Check-Out
- [ ] Scan same employee face again
- [ ] Verify: "Check-out successful" message
- [ ] Check database: check_out timestamp updated

### Test 4: Already Completed
- [ ] Scan same employee face third time
- [ ] Verify: "Attendance already completed today" message
- [ ] Check database: no new record created

### Test 5: Night Shift
- [ ] Assign employee to Night Shift
- [ ] Check-in during night shift hours
- [ ] Verify: date = shift start date
- [ ] Check-out next morning
- [ ] Verify: date remains same

### Test 6: Shift Management
- [ ] Go to `/admin/shifts`
- [ ] Verify: Day and Night shifts visible
- [ ] Create custom shift
- [ ] Edit shift times
- [ ] Delete custom shift

---

## Step 6: Frontend Verification

- [ ] Camera starts successfully
- [ ] Face detection works
- [ ] Check-in displays correctly
- [ ] Check-out displays correctly
- [ ] Status badges show correct colors
- [ ] Late minutes display correctly
- [ ] Attendance list updates in real-time

---

## Step 7: Database Verification

### Check Today's Attendance
```sql
SELECT 
    e.employee_id,
    e.full_name,
    a.date,
    a.check_in,
    a.check_out,
    a.status,
    a.late_minutes,
    s.shift_name
FROM attendance a
JOIN employees e ON a.employee_id = e.id
LEFT JOIN shift_types s ON e.shift_id = s.id
WHERE a.date = CURDATE();
```

- [ ] Run query
- [ ] Verify all fields populated correctly
- [ ] Verify late_minutes calculated correctly
- [ ] Verify night shift dates correct

---

## Step 8: Error Handling

### Test Error Scenarios
- [ ] Employee without shift_id → "No shift assigned"
- [ ] Employee without face_data → "Face not registered"
- [ ] Invalid employee_id → "Employee not found"
- [ ] Third attendance attempt → "Already completed"

---

## Step 9: Performance Check

- [ ] Face detection speed acceptable
- [ ] Database queries optimized
- [ ] No console errors
- [ ] No PHP errors in logs
- [ ] Page load time acceptable

**Check Logs**:
```bash
tail -f storage/logs/laravel.log
```

---

## Step 10: Documentation

- [ ] Read `README_FACE_ATTENDANCE.md`
- [ ] Read `CHANGES_SUMMARY.md`
- [ ] Read `FACE_ATTENDANCE_IMPLEMENTATION.md`
- [ ] Review `FLOW_DIAGRAM.md`
- [ ] Review `API_TESTING_GUIDE.md`
- [ ] Keep `database_setup.sql` for reference

---

## Step 11: User Training

- [ ] Train admin on shift management
- [ ] Train employees on face scanning
- [ ] Explain check-in/check-out process
- [ ] Explain late attendance policy
- [ ] Provide user manual

---

## Step 12: Monitoring

### First Week
- [ ] Monitor attendance records daily
- [ ] Check for any errors
- [ ] Verify late calculations
- [ ] Verify night shift handling
- [ ] Collect user feedback

### First Month
- [ ] Review attendance reports
- [ ] Optimize if needed
- [ ] Add custom shifts if requested
- [ ] Update documentation

---

## Rollback Plan (If Needed)

- [ ] Restore database backup
- [ ] Restore code backup
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Restart services

**Rollback SQL** (if needed):
```sql
-- Remove columns from attendance
ALTER TABLE attendance DROP COLUMN late_minutes;
ALTER TABLE attendance DROP COLUMN date;
ALTER TABLE attendance DROP COLUMN check_out;
ALTER TABLE attendance DROP COLUMN check_in;

-- Remove shift_id from employees
ALTER TABLE employees DROP FOREIGN KEY fk_employees_shift_id;
ALTER TABLE employees DROP COLUMN shift_id;

-- Drop shift_types table
DROP TABLE IF EXISTS shift_types;
```

---

## Post-Deployment

- [ ] Clear application cache
- [ ] Clear config cache
- [ ] Clear route cache
- [ ] Restart queue workers (if any)
- [ ] Monitor for 24 hours

**Commands**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Success Criteria

✅ All tests passed
✅ No errors in logs
✅ Users can check-in successfully
✅ Users can check-out successfully
✅ Late calculation working
✅ Night shift handling correct
✅ Admin can manage shifts
✅ Frontend displays correctly
✅ Database records accurate
✅ No breaking changes

---

## Sign-Off

**Deployed By**: ___________________
**Date**: ___________________
**Environment**: [ ] Development [ ] Staging [ ] Production
**Status**: [ ] Success [ ] Failed [ ] Rolled Back

**Notes**:
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

---

## Support Contacts

**Developer**: ___________________
**Database Admin**: ___________________
**System Admin**: ___________________

---

## Next Steps After Deployment

1. Monitor system for 1 week
2. Collect user feedback
3. Create monthly attendance reports
4. Plan future enhancements
5. Update documentation as needed

---

**Deployment Complete!** ✅

Keep this checklist for future reference and maintenance.
