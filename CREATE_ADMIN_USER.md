# How to Create the First Admin User

Since the admin panel is now complete, you need to create your first admin user to access it. Here are the methods:

## Method 1: Via Database (Recommended for First Admin)

1. Open your database management tool (phpMyAdmin, MySQL Workbench, etc.)
2. Find an existing user in the `users` table
3. Update their `role` column to `admin`

SQL Example:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

## Method 2: Via Laravel Tinker

1. Open terminal in your project directory
2. Run: `php artisan tinker`
3. Execute:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
```

## Method 3: Create New Admin User via Tinker

```php
php artisan tinker

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('your-secure-password'),
    'role' => 'admin'
]);
```

## Method 4: Via Database Seeder

Create a seeder file:
```bash
php artisan make:seeder AdminUserSeeder
```

Edit `database/seeders/AdminUserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
    }
}
```

Run the seeder:
```bash
php artisan db:seed --class=AdminUserSeeder
```

## After Creating Admin User

1. Log in with the admin credentials
2. You'll see an "Admin" link in the navigation bar
3. Click it to access the admin dashboard
4. From there, you can:
   - Create more admin users
   - Manage all users
   - Oversee all tasks and projects
   - Delete users, tasks, or projects

## Security Note

Make sure to use a strong password for admin accounts and keep the credentials secure!
