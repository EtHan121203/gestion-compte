-- Script to verify Sunday periods in database
-- SCRUM-16: ANALYSE-3 - Time counter bug analysis

-- Check for periods configured for Sunday (day_of_week = 6)
SELECT 
    p.id,
    p.day_of_week,
    p.start,
    p.end,
    COUNT(pp.id) as position_count,
    COUNT(s.id) as shift_count
FROM period p
LEFT JOIN period_position pp ON pp.period_id = p.id
LEFT JOIN shift s ON s.period_position_id = pp.id
WHERE p.day_of_week = 6
GROUP BY p.id, p.day_of_week, p.start, p.end;

-- Check for shifts on Sundays
SELECT 
    s.id,
    s.start,
    s.end,
    s.booked_time,
    s.was_carried_out,
    COUNT(r.id) as reservation_count
FROM shift s
LEFT JOIN registration r ON r.shift_id = s.id
WHERE DAYOFWEEK(s.start) = 1  -- 1 = Sunday in MySQL
GROUP BY s.id, s.start, s.end, s.booked_time, s.was_carried_out
ORDER BY s.start DESC
LIMIT 20;

-- Check TimeLogs related to Sunday shifts
SELECT 
    tl.id,
    tl.time,
    tl.date,
    tl.type,
    tl.description,
    m.member_number
FROM time_log tl
JOIN membership m ON tl.membership_id = m.id
JOIN shift s ON tl.description LIKE CONCAT('%', s.id, '%')
WHERE DAYOFWEEK(s.start) = 1
LIMIT 20;
