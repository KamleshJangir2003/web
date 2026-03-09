-- ============================================
-- FACE ATTENDANCE SYSTEM - DATABASE SETUP
-- ============================================
-- Run this if migrations fail or for manual setup

-- 1. CREATE SHIFT_TYPES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `shift_types` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `shift_name` VARCHAR(255) NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `late_after` INT DEFAULT 0 COMMENT 'Minutes after start_time',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. INSERT DEFAULT SHIFTS
-- ============================================
INSERT INTO `shift_types` (`shift_name`, `start_time`, `end_time`, `late_after`, `created_at`, `updated_at`) VALUES
('Day Shift', '09:30:00', '18:30:00', 0, NOW(), NOW()),
('Night Shift', '19:30:00', '05:10:00', 0, NOW(), NOW());

-- 3. ADD SHIFT_ID TO EMPLOYEES TABLE
-- ============================================
ALTER TABLE `employees` 
ADD COLUMN `shift_id` BIGINT UNSIGNED NULL AFTER `shift`,
ADD CONSTRAINT `fk_employees_shift_id` 
    FOREIGN KEY (`shift_id`) 
    REFERENCES `shift_types`(`id`) 
    ON DELETE SET NULL;

-- 4. ADD NEW COLUMNS TO ATTENDANCE TABLE
-- ============================================
ALTER TABLE `attendance` 
ADD COLUMN `check_in` DATETIME NULL AFTER `reason`,
ADD COLUMN `check_out` DATETIME NULL AFTER `check_in`,
ADD COLUMN `date` DATE NULL AFTER `check_out`,
ADD COLUMN `late_minutes` INT DEFAULT 0 AFTER `date`;

-- 5. ASSIGN DEFAULT SHIFTS TO EXISTING EMPLOYEES (OPTIONAL)
-- ============================================
-- Assign Day Shift to all active employees
UPDATE `employees` 
SET `shift_id` = 1 
WHERE `employee_status` = 'active' 
  AND `hired_status` = 'hired'
  AND `shift_id` IS NULL;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check shift_types table
SELECT * FROM shift_types;

-- Check employees with shifts
SELECT id, employee_id, full_name, shift_id 
FROM employees 
WHERE shift_id IS NOT NULL;

-- Check attendance table structure
DESCRIBE attendance;

-- ============================================
-- SAMPLE QUERIES FOR TESTING
-- ============================================

-- Assign specific shift to employee
UPDATE employees SET shift_id = 1 WHERE id = 1; -- Day Shift
UPDATE employees SET shift_id = 2 WHERE id = 2; -- Night Shift

-- Check today's attendance with late minutes
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
WHERE a.date = CURDATE()
ORDER BY a.check_in DESC;

-- Find late employees today
SELECT 
    e.employee_id,
    e.full_name,
    a.check_in,
    a.late_minutes,
    s.shift_name,
    s.start_time
FROM attendance a
JOIN employees e ON a.employee_id = e.id
JOIN shift_types s ON e.shift_id = s.id
WHERE a.date = CURDATE() 
  AND a.late_minutes > 0
ORDER BY a.late_minutes DESC;

-- ============================================
-- ROLLBACK (IF NEEDED)
-- ============================================

-- Remove columns from attendance
-- ALTER TABLE attendance DROP COLUMN late_minutes;
-- ALTER TABLE attendance DROP COLUMN date;
-- ALTER TABLE attendance DROP COLUMN check_out;
-- ALTER TABLE attendance DROP COLUMN check_in;

-- Remove shift_id from employees
-- ALTER TABLE employees DROP FOREIGN KEY fk_employees_shift_id;
-- ALTER TABLE employees DROP COLUMN shift_id;

-- Drop shift_types table
-- DROP TABLE IF EXISTS shift_types;
