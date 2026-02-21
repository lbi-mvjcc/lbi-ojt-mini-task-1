-- Check project team assignments
SELECT 
    p.id,
    p.name as project_name,
    fe.name as frontend_dev,
    be.name as backend_dev,
    sa.name as server_admin
FROM projects p
LEFT JOIN users fe ON p.frontend_developer_id = fe.id
LEFT JOIN users be ON p.backend_developer_id = be.id
LEFT JOIN users sa ON p.server_admin_id = sa.id;

-- Check task assignments
SELECT 
    t.id,
    t.title,
    t.category,
    p.name as project_name,
    u.name as assigned_to,
    u.role
FROM tasks t
JOIN projects p ON t.project_id = p.id
JOIN users u ON t.assigned_to = u.id
ORDER BY p.id, t.category;
