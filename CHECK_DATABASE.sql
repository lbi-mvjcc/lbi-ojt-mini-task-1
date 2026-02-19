-- Run these queries in phpMyAdmin to check your setup

-- 1. Check all users and their roles
SELECT id, name, email, role, created_at 
FROM users 
ORDER BY id;

-- 2. Check if any user has admin role
SELECT id, name, email, role 
FROM users 
WHERE role = 'admin';

-- 3. Check what roles exist in your database
SELECT DISTINCT role 
FROM users;

-- 4. If you need to make a user admin, run this:
-- UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';

-- 5. Or make the first user admin:
-- UPDATE users SET role = 'admin' WHERE id = 1;

-- 6. Verify the change:
-- SELECT id, name, email, role FROM users WHERE role = 'admin';
