-- Quick SQL Script to Make Yourself Admin
-- Run this in phpMyAdmin SQL tab

-- Option 1: If you know your email
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';

-- Option 2: Make the first user admin
UPDATE users SET role = 'admin' WHERE id = 1;

-- Option 3: Make a specific user admin by name
UPDATE users SET role = 'admin' WHERE name = 'Your Name';

-- Check if it worked
SELECT id, name, email, role FROM users WHERE role = 'admin';

-- After running this, LOG OUT and LOG BACK IN to your website!
