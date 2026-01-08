-- ============================================================================
-- Database Cleanup Script for SCRUM-16 (ANALYSE-3)
-- Purpose: Remove Sunday periods and related data to fix time counter bug
-- ============================================================================
-- IMPORTANT: This script should be reviewed and tested in a development
-- environment before running in production!
-- ============================================================================

-- Step 1: Analyze the impact (READ ONLY)
-- ============================================================================

-- Check how many periods exist for Sunday
SELECT 
    'Sunday Periods' as entity_type,
    COUNT(*) as count
FROM period 
WHERE day_of_week = 6;

-- Check how many positions are linked to Sunday periods
SELECT 
    'Period Positions (Sunday)' as entity_type,
    COUNT(*) as count
FROM period_position pp
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

-- Check how many shifts exist for Sunday
SELECT 
    'Sunday Shifts (by dayOfWeek)' as entity_type,
    COUNT(*) as count
FROM shift s
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

-- Alternative: Check shifts that fall on actual Sundays
SELECT 
    'Sunday Shifts (by date)' as entity_type,
    COUNT(*) as count
FROM shift s
WHERE DAYOFWEEK(s.start) = 1;  -- 1 = Sunday in MySQL

-- Check how many registrations exist for Sunday shifts
SELECT 
    'Registrations (Sunday shifts)' as entity_type,
    COUNT(*) as count
FROM registration r
JOIN shift s ON r.shift_id = s.id
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

-- Check TimeLogs potentially affected
SELECT 
    'TimeLogs (potentially affected)' as entity_type,
    COUNT(*) as count
FROM time_log tl
WHERE tl.type = 1  -- TYPE_SHIFT_VALIDATED
  AND tl.description LIKE '%Shift%'
  AND DAYOFWEEK(tl.date) = 1;  -- Sunday

-- ============================================================================
-- Step 2: Detailed analysis - List all Sunday periods
-- ============================================================================

SELECT 
    p.id as period_id,
    p.day_of_week,
    p.start as period_start_time,
    p.end as period_end_time,
    j.name as job_name,
    COUNT(DISTINCT pp.id) as positions_count,
    COUNT(DISTINCT s.id) as shifts_count,
    COUNT(DISTINCT r.id) as registrations_count
FROM period p
LEFT JOIN job j ON p.job_id = j.id
LEFT JOIN period_position pp ON pp.period_id = p.id
LEFT JOIN shift s ON s.period_position_id = pp.id
LEFT JOIN registration r ON r.shift_id = s.id
WHERE p.day_of_week = 6
GROUP BY p.id, p.day_of_week, p.start, p.end, j.name
ORDER BY p.id;

-- ============================================================================
-- Step 3: List affected shifts with details
-- ============================================================================

SELECT 
    s.id as shift_id,
    s.start as shift_start,
    s.end as shift_end,
    s.booked_time,
    s.was_carried_out,
    p.day_of_week,
    j.name as job_name,
    COUNT(r.id) as registration_count
FROM shift s
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
JOIN job j ON p.job_id = j.id
LEFT JOIN registration r ON r.shift_id = s.id
WHERE p.day_of_week = 6
GROUP BY s.id, s.start, s.end, s.booked_time, s.was_carried_out, p.day_of_week, j.name
ORDER BY s.start DESC
LIMIT 50;

-- ============================================================================
-- Step 4: List affected members (for communication)
-- ============================================================================

SELECT DISTINCT
    u.id as user_id,
    u.username,
    u.email,
    m.member_number,
    COUNT(DISTINCT r.id) as sunday_registrations_count
FROM registration r
JOIN shift s ON r.shift_id = s.id
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
JOIN membership m ON r.membership_id = m.id
JOIN user u ON m.main_beneficiary_id = u.id
WHERE p.day_of_week = 6
GROUP BY u.id, u.username, u.email, m.member_number
ORDER BY sunday_registrations_count DESC;

-- ============================================================================
-- Step 5: BACKUP COMMANDS (Run these BEFORE deletion)
-- ============================================================================

-- Create backup tables
CREATE TABLE IF NOT EXISTS period_backup_scrum16 AS 
SELECT * FROM period WHERE day_of_week = 6;

CREATE TABLE IF NOT EXISTS period_position_backup_scrum16 AS
SELECT pp.* FROM period_position pp
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

CREATE TABLE IF NOT EXISTS shift_backup_scrum16 AS
SELECT s.* FROM shift s
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

CREATE TABLE IF NOT EXISTS registration_backup_scrum16 AS
SELECT r.* FROM registration r
JOIN shift s ON r.shift_id = s.id
JOIN period_position pp ON s.period_position_id = pp.id
JOIN period p ON pp.period_id = p.id
WHERE p.day_of_week = 6;

CREATE TABLE IF NOT EXISTS time_log_backup_scrum16 AS
SELECT tl.* FROM time_log tl
WHERE tl.type = 1 AND DAYOFWEEK(tl.date) = 1;

-- ============================================================================
-- Step 6: DELETION COMMANDS (DANGEROUS - Review carefully!)
-- ============================================================================
-- IMPORTANT: These commands will permanently delete data. 
-- Make sure you have:
-- 1. Backed up the database
-- 2. Run the backup commands above
-- 3. Communicated with affected members
-- 4. Obtained approval from management
-- ============================================================================

-- Option A: Delete only future Sunday shifts (recommended for initial cleanup)
-- This preserves historical data but removes upcoming problematic shifts

-- Delete future registrations for Sunday shifts
-- DELETE r FROM registration r
-- JOIN shift s ON r.shift_id = s.id
-- JOIN period_position pp ON s.period_position_id = pp.id
-- JOIN period p ON pp.period_id = p.id
-- WHERE p.day_of_week = 6
--   AND s.start > NOW();

-- Delete future Sunday shifts
-- DELETE s FROM shift s
-- JOIN period_position pp ON s.period_position_id = pp.id
-- JOIN period p ON pp.period_id = p.id
-- WHERE p.day_of_week = 6
--   AND s.start > NOW();

-- Option B: Delete ALL Sunday data (more aggressive, cleaner result)
-- WARNING: This will delete historical data and affect time counters

-- Step 6.1: Delete registrations (must be done first due to foreign keys)
-- DELETE r FROM registration r
-- JOIN shift s ON r.shift_id = s.id
-- JOIN period_position pp ON s.period_position_id = pp.id
-- JOIN period p ON pp.period_id = p.id
-- WHERE p.day_of_week = 6;

-- Step 6.2: Delete shifts
-- DELETE s FROM shift s
-- JOIN period_position pp ON s.period_position_id = pp.id
-- JOIN period p ON pp.period_id = p.id
-- WHERE p.day_of_week = 6;

-- Step 6.3: Delete period positions
-- DELETE pp FROM period_position pp
-- JOIN period p ON pp.period_id = p.id
-- WHERE p.day_of_week = 6;

-- Step 6.4: Delete periods
-- DELETE FROM period WHERE day_of_week = 6;

-- Step 6.5: Delete or adjust TimeLogs (CAREFUL - affects time counters!)
-- Option A: Delete Sunday TimeLogs (will reduce time counters)
-- DELETE FROM time_log 
-- WHERE type = 1 
--   AND DAYOFWEEK(date) = 1;

-- Option B: Mark TimeLogs as corrections (preserves audit trail)
-- UPDATE time_log 
-- SET type = 0,  -- TYPE_CUSTOM
--     description = CONCAT('CORRECTION SCRUM-16: ', description),
--     time = -time  -- Reverse the time credit
-- WHERE type = 1 
--   AND DAYOFWEEK(date) = 1;

-- ============================================================================
-- Step 7: Verification (Run AFTER deletion)
-- ============================================================================

-- Verify no Sunday periods remain
SELECT COUNT(*) as remaining_sunday_periods FROM period WHERE day_of_week = 6;

-- Verify no orphaned period positions
SELECT COUNT(*) as orphaned_positions 
FROM period_position pp
LEFT JOIN period p ON pp.period_id = p.id
WHERE p.id IS NULL;

-- Verify no orphaned shifts
SELECT COUNT(*) as orphaned_shifts
FROM shift s
LEFT JOIN period_position pp ON s.period_position_id = pp.id
WHERE pp.id IS NULL;

-- ============================================================================
-- Step 8: Time Counter Recalculation (if TimeLogs were modified)
-- ============================================================================

-- After cleaning up Sunday data, members' time counters should be recalculated
-- This is done automatically by the application when viewing the membership page
-- Or can be triggered manually by running a Symfony command (if one exists)

-- Check which memberships had Sunday shifts
SELECT DISTINCT
    m.id,
    m.member_number,
    m.frozen,
    m.withdrawn
FROM membership m
JOIN registration r ON r.membership_id = m.id
WHERE r.id IN (
    SELECT r2.id FROM registration r2
    JOIN shift s ON r2.shift_id = s.id
    WHERE s.id IN (SELECT id FROM shift_backup_scrum16)
);

-- ============================================================================
-- Notes and Recommendations
-- ============================================================================

-- 1. RUN ANALYSIS QUERIES FIRST (Steps 1-4) to understand the scope
-- 2. COMMUNICATE with affected members before deletion
-- 3. BACKUP the database completely before any deletion
-- 4. RUN BACKUP COMMANDS (Step 5) before deletion
-- 5. TEST in a development environment first
-- 6. CHOOSE between Option A (future only) or Option B (all data)
-- 7. VERIFY results (Step 7) after deletion
-- 8. MONITOR time counters for a few days after cleanup
-- 9. KEEP backup tables for at least 30 days

-- ============================================================================
-- Rollback Plan (if something goes wrong)
-- ============================================================================

-- Restore periods
-- INSERT INTO period SELECT * FROM period_backup_scrum16;

-- Restore period positions
-- INSERT INTO period_position SELECT * FROM period_position_backup_scrum16;

-- Restore shifts
-- INSERT INTO shift SELECT * FROM shift_backup_scrum16;

-- Restore registrations
-- INSERT INTO registration SELECT * FROM registration_backup_scrum16;

-- Restore time logs
-- INSERT INTO time_log SELECT * FROM time_log_backup_scrum16;

-- ============================================================================
-- End of Script
-- ============================================================================
