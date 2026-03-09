-- DEBUG: Check if everything is set up correctly

-- 1. Check if shift_types table exists and has data
SELECT 'Shift Types:' as info;
SELECT * FROM shift_types;

-- 2. Check if shift_id column exists in employees
SELECT 'Employee Columns:' as info;
SHOW COLUMNS FROM employees LIKE 'shift_id';

-- 3. Check if attendance columns exist
SELECT 'Attendance Columns:' as info;
SHOW COLUMNS FROM attendance WHERE Field IN ('check_in', 'check_out', 'date', 'late_minutes');

-- 4. Check employees with face_data
SELECT 'Employees with Face Data:' as info;
SELECT id, employee_id, first_name, last_name, shift_id, 
       CASE WHEN face_data IS NOT NULL THEN 'YES' ELSE 'NO' END as has_face_data
FROM employees 
WHERE employee_status = 'active' 
  AND hired_status = 'hired';

-- 5. If no shifts assigned, assign Day Shift to all
UPDATE employees 
SET shift_id = 1 
WHERE employee_status = 'active' 
  AND hired_status = 'hired'
  AND shift_id IS NULL;

-- 6. Verify update
SELECT 'After Update:' as info;
SELECT id, employee_id, first_name, last_name, shift_id
FROM employees 
WHERE employee_status = 'active' 
  AND hired_status = 'hired';
