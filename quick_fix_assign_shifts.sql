-- QUICK FIX: Assign Day Shift to all active employees without shift

UPDATE employees 
SET shift_id = 1 
WHERE employee_status = 'active' 
  AND hired_status = 'hired'
  AND shift_id IS NULL;

-- Verify
SELECT id, employee_id, full_name, shift_id 
FROM employees 
WHERE employee_status = 'active' 
  AND hired_status = 'hired';
