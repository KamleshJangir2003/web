-- Testing Birthday Popup
-- Run this query to test birthday popup (replace ID with actual employee ID)

-- Check today's date format
SELECT CURDATE(), DATE_FORMAT(CURDATE(), '%m-%d') as today_format;

-- Update an employee's DOB to today (change ID = 1 to actual employee ID)
UPDATE employees 
SET dob = CURDATE() 
WHERE id = 1 
LIMIT 1;

-- Verify birthday employees for today
SELECT id, first_name, last_name, full_name, department, dob, 
       DATE_FORMAT(dob, '%m-%d') as dob_format,
       DATE_FORMAT(CURDATE(), '%m-%d') as today_format
FROM employees 
WHERE DATE_FORMAT(dob, "%m-%d") = DATE_FORMAT(CURDATE(), "%m-%d")
AND dob IS NOT NULL;

-- Clear cache after updating
-- Run: php artisan cache:clear
