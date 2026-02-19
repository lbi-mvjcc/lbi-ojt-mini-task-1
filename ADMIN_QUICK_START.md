# Admin Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1️⃣: Make Yourself Admin (One-Time Setup)

**Easiest Method - Using Database:**

1. Register an account on your website (if you don't have one)
2. Open your database tool (phpMyAdmin, etc.)
3. Go to the `users` table
4. Find your user by email
5. Change `role` from `customer` to `admin`
6. Save

**Alternative - Using Terminal:**
```bash
php artisan tinker
```
```php
$user = App\Models\User::where('email', 'YOUR_EMAIL')->first();
$user->role = 'admin';
$user->save();
```

### Step 2️⃣: Log In & Access Admin Panel

1. Log in to your website
2. Look at the top navigation bar
3. You'll see: **Dashboard | Task | Admin** ← Click this!
4. You're now in the admin panel! 🎉

### Step 3️⃣: Start Managing

**From the Admin Dashboard, you can:**

📊 **View Statistics**
- See total users, tasks, projects
- Monitor system activity

👥 **Manage Users** (Click "Manage Users")
- ➕ Create new users (any role: admin, customer, developer)
- ✏️ Edit user information and roles
- 🗑️ Delete users

📋 **Manage Tasks** (Click "Manage Tasks")
- 👁️ View all tasks in the system
- 🗑️ Delete any task

📁 **Manage Projects** (Click "Manage Projects")
- 👁️ View all projects
- 🗑️ Delete projects

---

## 📱 Admin Panel Navigation

```
Your Website
    └── Login as Admin
        └── Click "Admin" in navbar
            ├── Admin Dashboard (Overview)
            ├── Manage Users
            │   ├── View all users
            │   ├── Create new user
            │   ├── Edit user
            │   └── Delete user
            ├── Manage Tasks
            │   ├── View all tasks
            │   └── Delete task
            └── Manage Projects
                ├── View all projects
                └── Delete project
```

---

## 🎯 Common Admin Tasks

### Create a New User
1. Admin → Manage Users
2. Click "Create User" button
3. Fill in: Name, Email, Password
4. Select Role (Admin/Customer/Developer)
5. Click "Create User"

### Create Another Admin
1. Admin → Manage Users
2. Click "Create User"
3. Fill in details
4. Select Role: **"Administrator"**
5. Click "Create User"

### Change Someone's Role
1. Admin → Manage Users
2. Find the user
3. Click pencil icon (Edit)
4. Change the Role dropdown
5. Click "Update User"

### Delete a Task
1. Admin → Manage Tasks
2. Find the task
3. Click trash icon
4. Confirm deletion

### Delete a User
1. Admin → Manage Users
2. Find the user
3. Click trash icon
4. Confirm deletion
5. Note: Cannot delete yourself!

---

## 🔐 Available Roles

When creating/editing users, you can assign these roles:

| Role | Description | Access |
|------|-------------|--------|
| **Administrator** | Full system access | Everything + Admin Panel |
| **Customer** | Creates tasks & projects | Dashboard, Tasks, Projects |
| **Frontend Developer** | Works on frontend tasks | Dashboard, Assigned Tasks |
| **Backend Developer** | Works on backend tasks | Dashboard, Assigned Tasks |
| **Server Admin** | Manages server tasks | Dashboard, Assigned Tasks |

---

## ⚠️ Important Reminders

✅ **You can do:**
- Create unlimited users with any role
- Edit any user's information
- Delete any user (except yourself)
- View and delete all tasks and projects
- Change user roles anytime

❌ **You cannot:**
- Delete your own admin account (safety feature)
- Recover deleted items (deletions are permanent)

🔒 **Security:**
- Keep admin credentials secure
- Use strong passwords
- Only give admin role to trusted people

---

## 🆘 Troubleshooting

**Problem: I don't see the "Admin" link**
- Solution: Check your role is 'admin' in database, then log out and log back in

**Problem: I get "403 Unauthorized" error**
- Solution: Your role is not set to 'admin' - update it in the database

**Problem: I forgot my admin password**
- Solution: Reset it via database or create a new admin account

---

## 📞 Quick Reference

**Admin URLs:**
- Dashboard: `yoursite.com/admin/dashboard`
- Users: `yoursite.com/admin/users`
- Tasks: `yoursite.com/admin/tasks`
- Projects: `yoursite.com/admin/projects`

**Database Table:**
- Table: `users`
- Admin Role Value: `admin`

That's it! You're ready to manage your Task Management System! 🎉
