# Password Reset System Guide

## Overview
The password reset system has been updated to use admin-generated codes instead of email-based password resets. This provides better control and security for password resets.

## How It Works

### For Users (Forgot Password)
1. User goes to the "Forgot Password" page
2. User contacts the administrator to request a password reset code
3. Admin generates a 6-character code for the user
4. User enters their email, the code, and their new password
5. Password is reset successfully

### For Administrators
1. Login to the admin dashboard
2. Click on "Password Reset Codes" in the Quick Actions section
3. Select the user who needs a password reset
4. Click "Generate Code" to create a new 6-character code
5. Share the code with the user (valid for 24 hours)
6. View all generated codes and their status (Active, Used, Expired)

## Features
- **6-character codes**: Easy to communicate and enter
- **24-hour expiration**: Codes automatically expire after 24 hours
- **Single-use**: Each code can only be used once
- **Admin control**: Only administrators can generate reset codes
- **Code tracking**: View all generated codes with their status
- **Automatic invalidation**: When a new code is generated for a user, old unused codes are invalidated

## Database
A new table `password_reset_codes` has been created with the following fields:
- `id`: Primary key
- `user_id`: Foreign key to users table
- `code`: 6-character reset code
- `expires_at`: Expiration timestamp (24 hours from creation)
- `used`: Boolean flag indicating if code has been used
- `created_at` and `updated_at`: Timestamps

## Routes
- **Admin Routes**:
  - `GET /admin/password-reset-codes` - View all reset codes
  - `POST /admin/generate-reset-code` - Generate a new code
  
- **User Routes**:
  - `GET /forgot-password` - Password reset form
  - `POST /reset-password-with-code` - Reset password with code

## Files Modified/Created
1. `database/migrations/2026_02_21_220446_create_password_reset_codes_table.php` - Migration
2. `app/Models/PasswordResetCode.php` - Model with validation methods
3. `app/Http/Controllers/AdminController.php` - Added password reset methods
4. `app/Http/Controllers/Auth/PasswordResetLinkController.php` - Added resetWithCode method
5. `resources/views/admin/password-reset-codes.blade.php` - Admin interface
6. `resources/views/auth/forgot-password.blade.php` - Updated user interface
7. `resources/views/admin/dashboard.blade.php` - Added link to password reset codes
8. `routes/web.php` - Added admin routes
9. `routes/auth.php` - Added password reset with code route

## Security Features
- Only administrators can generate codes
- Codes expire after 24 hours
- Codes are single-use only
- Old unused codes are automatically invalidated when new ones are generated
- User must provide correct email and code combination
