-- Check all tasks for a specific customer
-- Replace 'customer_email@example.com' with the actual customer email

SELECT 
    t.id,
    t.title,
    t.category,
    t.status,
    p.name as project_name,
    u.name as assigned_developer,
    u.role as developer_role,
    t.created_at
FROM tasks t
JOIN projects p ON t.project_id = p.id
JOIN users u ON t.assigned_to = u.id
JOIN users c ON t.created_by = c.id
WHERE c.email = 'customer_email@example.com'  -- Change this to your customer's email
ORDER BY p.id, t.created_at;

-- Count tasks by category for each project
SELECT 
    p.name as project_name,
    t.category,
    COUNT(*) as task_count
FROM tasks t
JOIN projects p ON t.project_id = p.id
JOIN users c ON t.created_by = c.id
WHERE c.email = 'customer_email@example.com'  -- Change this to your customer's email
GROUP BY p.id, p.name, t.category
ORDER BY p.name, t.category;
