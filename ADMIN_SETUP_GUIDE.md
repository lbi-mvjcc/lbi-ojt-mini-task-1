# Admin Setup & Management Guide

## Step 1: Create Your First Admin User

Since you can't create an admin through the registration page, you need to manually create one first. Here's the easiest way:

### Option A: Update Existing User (Easiest)

1. **Register a normal account** through your website (if you haven't already)
2. **Open your database** (using phpMyAdmin, TablePlus, or any database tool)
3. **Find the `users` table**
4. **Find your user** by email
5. **Change the `role` column** from `customer` to `admin`
6. **Save the changes**
7. **Log out and log back in**

### Option B: Use Command Line (Laravel Tinker)

1. Open terminal in your project folder
2. Run this command:
```bash
php artisan tinker
```

3. Then type this (replace with your email):
```php
$user = App\Models\User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
exit
```

### Option C: Create New Admin via Tinker

```bash
php artisan tinker
```

Then:
```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin'
]);
exit
```

## Step 2: Access Admin Panel

1. **Log in** with your admin account
2. Look at the **navigation bar** - you'll see an "Admin" link with a shield icon
3. **Click "Admin"** to access the admin dashboard

## Step 3: What You Can Do as Admin

### Admin Dashboard
- View statistics (total users, tasks, projects)
- See recent activity
- Quick access to all management sections

### User Management
**To view all users:**
- Click "Manage Users" or go to `/admin/users`
- See all registered users with their roles

**To create a new user:**
- Click "Create User" button
- Fill in: Name, Email, Role, Password
- Choose role: Admin, Customer, Frontend Dev, Backend Dev, or Server Admin
- Click "Create User"

**To edit a user:**
- Click the pencil icon next to any user
- Update their information
- Change their role if needed
- Optionally change their password
- Click "Update User"

**To delete a user:**
- Click the trash icon next to any user
- Confirm deletion
- Note: You cannot delete yourself!

### Task Management
**To view all tasks:**
- Click "Manage Tasks" or go to `/admin/tasks`
- See all tasks from all users

**To delete a task:**
- Click the trash icon next to any task
- Confirm deletion

**To view task details:**
- Click the eye icon to see full task information

### Project Management
**To view all projects:**
- Click "Manage Projects" or go to `/admin/projects`
- See all projects from all customers

**To delete a project:**
- Click the trash icon
- Confirm deletion
- Warning: This will also delete all tasks in that project!

## Step 4: Create More Admins

Once you're logged in as admin:

1. Go to **Admin → Manage Users**
2. Click **"Create User"**
3. Fill in the details
4. Select **"Administrator"** as the role
5. Click **"Create User"**

Now you have another admin!

## Admin Features Summary

✅ **Full User Control**
- Create users with any role
- Edit user information and roles
- Delete users (except yourself)
- View user statistics

✅ **Task Oversight**
- View all tasks in the system
- Delete any task
- See task details, status, priority

✅ **Project Oversight**
- View all projects
- Delete projects
- See project details and task counts

✅ **Statistics Dashboard**
- Total users by role
- Task statistics (pending, in progress, completed)
- Recent activity tracking

## Important Notes

⚠️ **Security Tips:**
- Use strong passwords for admin accounts
- Don't share admin credentials
- Regularly review user accounts
- Be careful when deleting - it cannot be undone!

⚠️ **Self-Protection:**
- You cannot delete your own admin account
- This prevents accidental lockout

⚠️ **Deletion Warnings:**
- Deleting a user doesn't delete their tasks
- Deleting a project deletes all its tasks
- All deletions show confirmation dialogs

## Quick Access URLs

Once logged in as admin:
- Admin Dashboard: `/admin/dashboard`
- User Management: `/admin/users`
- Create User: `/admin/users/create`
- Task Management: `/admin/tasks`
- Project Management: `/admin/projects`

## Troubleshooting

**"I don't see the Admin link"**
- Make sure your user's role is set to 'admin' in the database
- Log out and log back in
- Check the `role` column in the `users` table

**"I get 403 Unauthorized error"**
- Your account doesn't have admin role
- Update your role to 'admin' in the database

**"I can't delete a user"**
- You cannot delete yourself
- Make sure you're not trying to delete your own account

## Need Help?

If you have any issues:
1. Check that your role is 'admin' in the database
2. Clear your browser cache
3. Log out and log back in
4. Check the Laravel logs in `storage/logs/laravel.log`
