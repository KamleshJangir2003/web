-- Set employee birthday to today (25 February) for testing
-- Run this in your database

UPDATE employees 
SET dob = '2026-02-25' 
WHERE id = 1 
LIMIT 1;

-- Verify the update
SELECT id, first_name, last_name, full_name, department, dob,
       DATE_FORMAT(dob, '%m-%d') as birthday_format
FROM employees 
WHERE DATE_FORMAT(dob, "%m-%d") = '02-25';

-- After running this:
-- 1. Clear cache: php artisan cache:clear
-- 2. Reload dashboard
-- 3. Popup will show automatically after 1 second
