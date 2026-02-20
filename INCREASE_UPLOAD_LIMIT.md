# How to Increase File Upload Limit

## Current Issue
You're getting "The POST data is too large" error because PHP's default upload limit is about 8 MB, but your application allows up to 50 MB.

## Solution: Update PHP Configuration

### Step 1: Find your php.ini file

**Option A: Using Command Line**
```bash
php --ini
```
Look for "Loaded Configuration File"

**Option B: Common Locations**
- XAMPP: `C:\xampp\php\php.ini`
- WAMP: `C:\wamp64\bin\php\php8.x.x\php.ini`
- Laragon: `C:\laragon\bin\php\php-8.x.x\php.ini`

### Step 2: Edit php.ini

Open `php.ini` in a text editor (as Administrator) and find these lines:

```ini
upload_max_filesize = 2M
post_max_size = 8M
max_execution_time = 30
max_input_time = 60
memory_limit = 128M
```

Change them to:

```ini
upload_max_filesize = 50M
post_max_size = 55M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

**Important Notes:**
- `post_max_size` should be slightly larger than `upload_max_filesize`
- `max_execution_time` = how long PHP can run (5 minutes for large uploads)
- `memory_limit` = RAM PHP can use

### Step 3: Restart PHP Server

**If using built-in PHP server:**
1. Stop the server (Ctrl+C)
2. Start it again: `php -S 127.0.0.1:8888 -t public`

**If using XAMPP/WAMP:**
1. Stop Apache
2. Start Apache again

### Step 4: Verify Changes

Create a file `test-upload-limit.php` in your `public` folder:

```php
<?php
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . " seconds<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
```

Visit: `http://127.0.0.1:8888/test-upload-limit.php`

You should see:
```
Upload Max Filesize: 50M
Post Max Size: 55M
Max Execution Time: 300 seconds
Memory Limit: 256M
```

Delete the test file after verifying.

---

## Alternative: Quick Fix for Development

If you can't find or edit php.ini, you can set limits in your Laravel code:

**Add to `public/index.php` (at the top, after `<?php`):**

```php
<?php
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '55M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

// Rest of the file...
```

**Note:** This only works if your hosting allows `ini_set()`. Some shared hosts disable it.

---

## What I Fixed in the Code

1. **Added client-side validation** - Now checks file size BEFORE uploading
2. **Shows file size in error** - Tells user exactly how big their file is
3. **Clears file input** - Prevents accidental re-upload of large file
4. **Better warning UI** - Yellow warning box with icon instead of small text

Now when you select a file larger than 50 MB, you'll see:
```
File size (87.5 MB) exceeds the maximum allowed size of 50 MB. 
Please choose a smaller file.
```

---

## For Your Presentation

If supervisor asks about file upload limits:

**Answer:**
"We set a 50 MB limit in the application for task attachments. This requires configuring PHP's `upload_max_filesize` and `post_max_size` settings. We also added client-side validation to check file size before uploading, giving users immediate feedback if their file is too large. This prevents wasted time uploading files that will be rejected."

**Show them:**
1. The yellow warning box in the upload form
2. Try uploading a large file to demonstrate the error message
3. Explain that PHP limits can be adjusted in production based on needs
