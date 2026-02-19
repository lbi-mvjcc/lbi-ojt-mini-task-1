# Check Your Admin Status

## Quick Diagnostic Steps

### Step 1: Check Your Role in Database

1. Open phpMyAdmin
2. Go to the `users` table
3. Find your user (the one you're logged in with)
4. Check the `role` column - it MUST say **"admin"** (not "customer")

### Step 2: If Role is NOT "admin"

**Fix it now:**
1. Click "Edit" on your user
2. Change `role` to **"admin"**
3. Click "Go" to save

**OR run this SQL:**
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

### Step 3: Log Out and Log Back In

This is CRITICAL! Laravel caches your user session.

1. Click your profile dropdown in the top right
2. Click "Logout"
3. Log back in with the same credentials
4. Now check the navigation bar - you should see "Admin"

### Step 4: Test Admin Access

1. Click the "Admin" link in the navigation
2. You should see the admin dashboard with statistics
3. Try clicking "Manage Users" - you should see all users

## Common Issues & Solutions

### Issue 1: "403 Unauthorized" Error
**Cause:** Your role is not set to 'admin' in the database
**Solution:** 
1. Check database - make sure role = 'admin'
2. Log out and log back in

### Issue 2: "Admin link not showing in navigation"
**Cause:** You haven't logged out after changing role
**Solution:** 
1. Log out completely
2. Log back in
3. The Admin link should appear

### Issue 3: "No details in admin page"
**Cause:** Database might be empty or role check failing
**Solution:**
1. Make sure you have some users/tasks/projects in database
2. Verify your role is 'admin'
3. Clear browser cache (Ctrl+Shift+Delete)
4. Log out and log back in

### Issue 4: "Other buttons show unauthorized"
**Cause:** Session hasn't refreshed with new admin role
**Solution:**
1. Log out (important!)
2. Close browser completely
3. Open browser again
4. Log back in
5. Try accessing admin panel

## Verify Your Setup

Run this in your terminal to check if admin role exists:

```bash
php artisan tinker
```

Then:
```php
// Check if your user is admin
$user = App\Models\User::where('email', 'your-email@example.com')->first();
echo "Role: " . $user->role;
echo "\nIs Admin: " . ($user->isAdmin() ? 'YES' : 'NO');
exit
```

## Expected Results

When everything is working correctly:

✅ **Navigation Bar:** Shows "Dashboard | Task | Admin"
✅ **Admin Dashboard:** Shows statistics (total users, tasks, projects)
✅ **Manage Users:** Shows list of all users
✅ **Create User:** Shows form to create new users
✅ **No 403 Errors:** All admin pages load properly

## Still Having Issues?

If you're still getting errors, please check:

1. **Did you run the migration?**
   ```bash
   php artisan migrate
   ```

2. **Is your role exactly 'admin'?** (not 'Admin' or 'ADMIN')
   - Must be lowercase: `admin`

3. **Did you log out and log back in?**
   - This is the most common issue!

4. **Clear your browser cache:**
   - Press Ctrl+Shift+Delete
   - Clear cookies and cached data
   - Close and reopen browser

5. **Check Laravel logs:**
   - Look in `storage/logs/laravel.log`
   - See if there are any error messages

## Quick Fix Command

If nothing works, run this to force your user to admin:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
echo "Done! Now log out and log back in.";
exit
```

---

**Remember:** After changing your role to admin, you MUST log out and log back in for the changes to take effect!
