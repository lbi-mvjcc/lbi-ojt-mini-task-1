# Backend Code Explanation Guide

## 1. SMART TASK ASSIGNMENT (Most Important!)

**Location:** `app/Models/Task.php` - `autoAssignDeveloper()` method

**What it does:** Automatically assigns tasks to developers based on their workload and role.

**Algorithm Type:** Least-Loaded Distribution (Dynamic Load Balancing)
- NOT round-robin (which assigns in fixed rotation)
- Assigns to the developer with the LEAST active tasks
- Adapts in real-time based on who completes work
- More efficient than round-robin because fast workers get more tasks immediately

**Key Logic:**
```php
public function autoAssignDeveloper()
{
    // Step 1: Check if this project already has a developer for this category
    $existingDeveloper = Task::where('project_id', $this->project_id)
        ->where('category', $this->category)
        ->whereNotNull('assigned_to')
        ->first();

    if ($existingDeveloper) {
        // Use the same developer for consistency
        $this->assigned_to = $existingDeveloper->assigned_to;
    } else {
        // Step 2: Find the least busy developer with matching role
        $roleMap = [
            'frontend' => 'Frontend Developer',
            'backend' => 'Backend Developer',
            'server' => 'Server Administrator',
        ];

        $developer = User::where('role', $roleMap[$this->category])
            ->withCount(['assignedTasks' => function($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            }])
            ->orderBy('assigned_tasks_count', 'asc')
            ->first();

        $this->assigned_to = $developer->id;
    }
}
```

**Explain it like this:**
- "First, we check if the project already has a developer for this category (frontend/backend/server)"
- "If yes, we assign to the same developer to keep consistency"
- "If no, we find the developer with the least workload using `withCount()` and `orderBy()`"
- "This ensures fair distribution and one developer per category per project"

**What happens with the FIRST task ever (fresh system)?**
- All developers have 0 tasks (assigned_tasks_count = 0)
- `orderBy('assigned_tasks_count', 'asc')` sorts them, but they're all tied at 0
- `->first()` picks the first one from the sorted list
- **Result: The first developer in the database gets the first task**
- For example: If it's a frontend task, "Frontend Developer 1" gets it (because they were created first in the seeder)
- The second frontend task in a different project will go to "Frontend Developer 2" (who now has 0 tasks vs Developer 1's 1 task)
- This naturally distributes tasks evenly across all developers

**IMPORTANT: Only counts ACTIVE tasks (pending + in_progress)**
- Completed tasks are NOT counted in the workload
- If Developer 2 finishes their task first, they become available (count = 0)
- The next task will go to Developer 2, even if Developer 1 still has an active task
- This ensures tasks go to developers who are currently available, not just based on total assignments

**Why this is BETTER than Round-Robin:**

Round-Robin (fixed rotation):
- Dev 1 → Dev 2 → Dev 3 → Dev 4 → Dev 5 → repeat
- Doesn't care if someone finishes early
- Fast workers sit idle waiting for their turn
- Slow workers get overloaded

Your System (Least-Loaded):
- Always assigns to whoever has the fewest active tasks RIGHT NOW
- Fast workers immediately get more tasks (no idle time)
- Slow workers don't get overloaded (keep current tasks)
- Dynamically adapts to real-time performance

**Example scenario:**
1. Fresh system: All 5 Frontend Devs have 0 active tasks
2. Task 1 → Frontend Dev 1 (0 active tasks)
3. Task 2 (different project) → Frontend Dev 2 (0 active, Dev 1 has 1)
4. Task 3 (different project) → Frontend Dev 3 (0 active)
5. Task 4 (different project) → Frontend Dev 4 (0 active)
6. Task 5 (different project) → Frontend Dev 5 (0 active)
7. **Dev 2 completes their task** → Now has 0 active tasks
8. Task 6 (different project) → Frontend Dev 2 (0 active, others have 1)
9. This creates a natural load balancing based on who finishes work fastest!

---

## 2. USER ROLES & AUTHORIZATION

**Location:** `app/Models/User.php`

**What it does:** Defines user roles and helper methods to check permissions.

**Key Code:**
```php
public function isCustomer()
{
    return $this->role === 'Customer';
}

public function isFrontendDeveloper()
{
    return $this->role === 'Frontend Developer';
}

public function isBackendDeveloper()
{
    return $this->role === 'Backend Developer';
}

public function isServerAdministrator()
{
    return $this->role === 'Server Administrator';
}
```

**Explain it like this:**
- "We have 4 roles: Customer, Frontend Developer, Backend Developer, Server Administrator"
- "These helper methods make it easy to check permissions in controllers"
- "Example: `if ($user->isCustomer())` - only customers can create projects"

---

## 3. DATA ISOLATION (Security!)

**Location:** `app/Http/Controllers/TaskController.php` - `index()` method

**What it does:** Ensures users only see their own data.

**Key Code:**
```php
public function index()
{
    $user = auth()->user();
    
    if ($user->isCustomer()) {
        // Customers see only their created tasks
        $tasks = Task::with(['project', 'assignedUser'])
            ->where('customer_id', $user->id)
            ->latest()
            ->get();
    } else {
        // Developers see only tasks assigned to them
        $tasks = Task::with(['project', 'customer'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();
    }
}
```

**Explain it like this:**
- "We use `where('customer_id', $user->id)` to filter by logged-in user"
- "Customers only see tasks they created"
- "Developers only see tasks assigned to them"
- "This prevents users from seeing other people's data"

---

## 4. SOFT DELETES (Trash System)

**Location:** `app/Models/Project.php` and `app/Models/Task.php`

**What it does:** Marks records as deleted without actually removing them from database.

**Key Code:**
```php
use SoftDeletes;

protected $dates = ['deleted_at'];
```

**In Controllers:**
```php
// Delete (soft delete)
$project->delete(); // Sets deleted_at timestamp

// Restore
$project->restore(); // Sets deleted_at to null

// Permanent delete
$project->forceDelete(); // Actually removes from database

// Get only deleted items
$deleted = Project::onlyTrashed()->get();
```

**Explain it like this:**
- "Soft delete adds a `deleted_at` timestamp instead of removing the record"
- "Users can restore deleted items from the Trash page"
- "Force delete permanently removes the record"
- "This is safer than hard deletes and allows undo functionality"

---

## 5. NOTIFICATIONS SYSTEM

**Location:** `app/Http/Middleware/HandleInertiaRequests.php`

**What it does:** Tracks unread comments and attachments as notifications.

**Key Code:**
```php
public function share(Request $request): array
{
    $notifications = [];
    
    if ($user && $user->isCustomer()) {
        // Get unread comments
        $unreadComments = TaskComment::whereHas('task', function($query) use ($user) {
            $query->where('customer_id', $user->id);
        })
        ->whereNull('read_at')
        ->with(['task.project'])
        ->latest()
        ->get();

        // Get unread attachments
        $unreadAttachments = TaskAttachment::whereHas('task', function($query) use ($user) {
            $query->where('customer_id', $user->id);
        })
        ->whereNull('read_at')
        ->with(['task.project'])
        ->latest()
        ->get();

        $notifications = [
            'comments' => $unreadComments,
            'attachments' => $unreadAttachments,
            'total' => $unreadComments->count() + $unreadAttachments->count(),
        ];
    }
    
    return [
        'auth' => ['user' => $user],
        'notifications' => $notifications,
    ];
}
```

**Explain it like this:**
- "We use `read_at` column to track if notification is read (null = unread)"
- "Middleware shares notifications with all pages automatically"
- "We count unread comments + attachments for the badge number"
- "When user clicks notification, we set `read_at` to current timestamp"

---

## 6. ELOQUENT RELATIONSHIPS

**Location:** `app/Models/Task.php`, `app/Models/Project.php`, `app/Models/User.php`

**What it does:** Defines how tables are connected.

**Key Relationships:**

**Task Model:**
```php
public function project()
{
    return $this->belongsTo(Project::class);
}

public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}

public function assignedUser()
{
    return $this->belongsTo(User::class, 'assigned_to');
}
```

**Project Model:**
```php
public function tasks()
{
    return $this->hasMany(Task::class);
}

public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}
```

**User Model:**
```php
public function projects()
{
    return $this->hasMany(Project::class, 'customer_id');
}

public function assignedTasks()
{
    return $this->hasMany(Task::class, 'assigned_to');
}
```

**Explain it like this:**
- "`belongsTo` = this record belongs to one parent (Task belongs to Project)"
- "`hasMany` = this record has multiple children (Project has many Tasks)"
- "This allows us to do `$task->project->name` or `$project->tasks`"
- "Eager loading with `with()` prevents N+1 query problems"

---

## 7. VALIDATION

**Location:** `app/Http/Requests/Settings/ProfileUpdateRequest.php` and similar

**What it does:** Validates user input before saving to database.

**Key Code:**
```php
public function rules()
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
    ];
}
```

**In Controllers:**
```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'category' => 'required|in:frontend,backend,server',
    'project_id' => 'required|exists:projects,id',
]);
```

**Explain it like this:**
- "Validation happens before data reaches the database"
- "`required` = field must be present"
- "`exists:projects,id` = checks if project ID exists in database"
- "`unique:users,email` = ensures no duplicate emails"
- "This prevents invalid data and SQL injection"

---

## 8. FILE UPLOADS

**Location:** `app/Http/Controllers/TaskController.php` - `storeAttachment()` method

**What it does:** Handles file uploads for task attachments (photos, videos, files, links).

**File Size Limits:**
- **Task Attachments** (photos, videos, files): **50 MB maximum**
- **Profile Pictures**: **2 MB maximum**

**Key Code:**
```php
public function storeAttachment(Request $request, Task $task)
{
    $validated = $request->validate([
        'type' => 'required|in:link,file,photo,video',
        'link_url' => 'required_if:type,link|url|max:500',
        'file' => 'required_unless:type,link|file|max:51200', // 50MB = 51200 KB
    ], [
        'file.max' => 'File size must not exceed 50MB.',
    ]);

    if ($validated['type'] === 'link') {
        // Store link URL in database
        TaskAttachment::create([
            'task_id' => $task->id,
            'type' => 'link',
            'url' => $validated['link_url'],
        ]);
    } else {
        // Upload file to storage/app/public/task_attachments
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('task_attachments', $fileName, 'public');

        TaskAttachment::create([
            'task_id' => $task->id,
            'type' => $validated['type'],
            'name' => $file->getClientOriginalName(),
            'url' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }
}
```

**Profile Picture Validation:**
```php
'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB
```

**File Storage:**
- Task attachments: `storage/app/public/task_attachments/`
- Profile pictures: `storage/app/public/profile_pictures/`
- Files are renamed with timestamp to avoid conflicts: `1234567890_filename.jpg`

**Allowed File Types:**
- **Task Attachments**: Any file type (photos, videos, documents, etc.)
- **Profile Pictures**: Only images (jpeg, png, jpg, gif)

**Explain it like this:**
- "Task attachments can be up to 50 MB - large enough for videos and documents"
- "Profile pictures are limited to 2 MB to keep the system fast"
- "We validate file type (image only) and size (max 2MB) for profile pictures"
- "Old files are automatically deleted when uploading new ones to save storage space"
- "`store()` saves files to `storage/app/public/` directory"
- "We save the file path in database, not the actual file"
- "Files are renamed with timestamps to prevent naming conflicts"

**Why these limits?**
- 50 MB for task attachments: Allows developers to upload work samples, videos, screenshots
- 2 MB for profile pictures: Keeps page load times fast while allowing good quality images
- These limits can be increased in production if needed

**How to change limits:**
1. Update validation rule in controller
2. Update PHP settings in `php.ini`:
   - `upload_max_filesize = 50M`
   - `post_max_size = 50M`
3. Update web server settings (if using nginx/apache)

---

## 9. CASCADE DELETE

**Location:** `app/Http/Controllers/ProjectController.php` - `destroy()` method

**What it does:** When deleting a project, also delete all its tasks.

**Key Code:**
```php
public function destroy(Project $project)
{
    // Soft delete all tasks in this project
    Task::where('project_id', $project->id)->delete();
    
    // Soft delete the project
    $project->delete();
    
    return redirect()->route('projects.index');
}
```

**Explain it like this:**
- "When a project is deleted, all its tasks are also deleted"
- "Both use soft delete, so they can be restored from trash"
- "This maintains data integrity - no orphaned tasks"

---

## 10. AUTHENTICATION & LOGIN TRACKING

**Location:** `app/Providers/FortifyServiceProvider.php`

**What it does:** Tracks how many times user has logged in.

**Key Code:**
```php
Fortify::authenticateUsing(function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        // Increment login count
        $user->increment('login_count');
        return $user;
    }
});
```

**Explain it like this:**
- "Every successful login increments the `login_count` column"
- "First login shows 'Welcome', subsequent logins show 'Welcome back'"
- "Uses Laravel Fortify for authentication"
- "Password is hashed with bcrypt for security"

---

## 11. DEVELOPER ACCOUNTS CREATION

**Location:** `database/seeders/ProjectSeeder.php`

**What it does:** Creates test accounts for developers and customers when seeding the database.

**Key Code:**
```php
public function run(): void
{
    // Create 1 customer
    $customer = User::create([
        'name' => 'John Customer',
        'email' => 'customer@example.com',
        'password' => bcrypt('password'),
        'role' => 'customer',
    ]);

    // Create 5 Frontend Developers
    for ($i = 1; $i <= 5; $i++) {
        User::create([
            'name' => "Frontend Developer $i",
            'email' => "frontend$i@example.com",
            'password' => bcrypt('password'),
            'role' => 'frontend_developer',
        ]);
    }

    // Create 5 Backend Developers
    for ($i = 1; $i <= 5; $i++) {
        User::create([
            'name' => "Backend Developer $i",
            'email' => "backend$i@example.com",
            'password' => bcrypt('password'),
            'role' => 'backend_developer',
        ]);
    }

    // Create 5 Server Administrators
    for ($i = 1; $i <= 5; $i++) {
        User::create([
            'name' => "Server Admin $i",
            'email' => "server$i@example.com",
            'password' => bcrypt('password'),
            'role' => 'server_administrator',
        ]);
    }
}
```

**How to run it:**
```bash
php artisan db:seed
# or
php artisan migrate:fresh --seed
```

**Explain it like this:**
- "We created a seeder file to populate the database with test accounts"
- "It creates 5 Frontend Developers, 5 Backend Developers, and 5 Server Administrators"
- "All accounts use the password 'password' for testing"
- "Email format: frontend1@example.com, backend1@example.com, server1@example.com"
- "The seeder runs automatically when we use `php artisan migrate:fresh --seed`"

**Login Credentials:**
- Customer: `customer@example.com` / `password`
- Frontend Dev 1: `frontend1@example.com` / `password`
- Backend Dev 1: `backend1@example.com` / `password`
- Server Admin 1: `server1@example.com` / `password`
- (And so on for developers 2-5)

---

## 12. DATABASE LOCATION & STRUCTURE

**Database Type:** SQLite (file-based database)

**Location:** `database/database.sqlite`

**What is SQLite?**
- A lightweight, file-based database
- No separate server needed (unlike MySQL)
- Perfect for development and small applications
- The entire database is stored in a single file

**How to view the database:**

1. **Using DB Browser for SQLite (Recommended):**
   - Download from: https://sqlitebrowser.org/
   - Open the file: `database/database.sqlite`
   - You can view all tables, data, and run SQL queries

2. **Using Command Line:**
   ```bash
   sqlite3 database/database.sqlite
   .tables          # Show all tables
   .schema users    # Show table structure
   SELECT * FROM users;  # Query data
   .quit            # Exit
   ```

3. **Using Laravel Tinker:**
   ```bash
   php artisan tinker
   User::all();     # Get all users
   Task::count();   # Count tasks
   ```

**Database Tables:**
- `users` - All user accounts (customers, developers)
- `projects` - Customer projects
- `tasks` - Tasks with assignments
- `task_comments` - Developer comments on tasks
- `task_attachments` - File uploads (photos, videos, files, links)
- `project_members` - (Not actively used in current system)
- `cache` - Application cache
- `jobs` - Background job queue
- `sessions` - User sessions

**Database Configuration:**
Located in `.env` file:
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\Admin\logicbase-projects\TaskManagement\database\database.sqlite
```

**Can it work with MySQL?**
Yes! Just change `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run:
```bash
php artisan migrate
php artisan db:seed
```

**Explain it like this:**
- "We use SQLite, which stores the entire database in a single file at `database/database.sqlite`"
- "It's perfect for development because it doesn't require installing MySQL or PostgreSQL"
- "The database file is currently 128 KB (0.12 MB) and contains all users, projects, tasks, and comments"
- "For production, we can easily switch to MySQL by just changing the `.env` configuration"
- "Laravel's Eloquent ORM makes the code database-agnostic - it works with any database"

**Database Size Check:**
You can check the current size by looking at the file properties of `database/database.sqlite`

---

## QUICK REFERENCE - FILE LOCATIONS

### Controllers (Business Logic)
- `app/Http/Controllers/TaskController.php` - Task CRUD operations
- `app/Http/Controllers/ProjectController.php` - Project CRUD operations
- `app/Http/Controllers/Settings/ProfileController.php` - User profile management

### Models (Database & Relationships)
- `app/Models/User.php` - User roles and relationships
- `app/Models/Task.php` - Task assignment logic
- `app/Models/Project.php` - Project relationships
- `app/Models/TaskComment.php` - Comments
- `app/Models/TaskAttachment.php` - File attachments

### Middleware (Shared Data)
- `app/Http/Middleware/HandleInertiaRequests.php` - Notifications system

### Migrations (Database Structure)
- `database/migrations/2026_02_12_084508_create_tasks_table.php` - Tasks table
- `database/migrations/2026_02_16_030842_add_soft_deletes_to_projects_and_tasks_tables.php` - Soft deletes
- `database/migrations/2026_02_16_065849_add_profile_picture_to_users_table.php` - Profile pictures

### Seeders (Test Data)
- `database/seeders/ProjectSeeder.php` - Creates 15 developer accounts (5 FE, 5 BE, 5 SA) + 1 customer

### Database
- `database/database.sqlite` - SQLite database file (contains all data)
- `database/migrations/` - Database schema definitions
- `.env` - Database configuration

### Routes
- `routes/web.php` - All application routes

---

## TIPS FOR PRESENTATION

1. **Start with the big picture:** "Our system has 4 user roles and automatically assigns tasks to developers"

2. **Show the smart assignment:** Open `app/Models/Task.php` and explain the `autoAssignDeveloper()` method

3. **Explain security:** Show how we filter data by user ID in controllers

4. **Demonstrate relationships:** Show how `$task->project->name` works

5. **Highlight soft deletes:** Explain the trash/restore functionality

6. **Be ready for these questions:**
   - "How do you prevent SQL injection?" → "Laravel uses prepared statements automatically"
   - "How do you handle concurrent requests?" → "Database transactions and locks"
   - "Why Eloquent instead of raw SQL?" → "Cleaner code, prevents SQL injection, easier relationships"

---

## COMMON SUPERVISOR QUESTIONS & ANSWERS

**Q: "How does the task assignment work?"**
A: "We use a Least-Loaded Distribution algorithm, also called Dynamic Load Balancing. It's better than round-robin because it assigns tasks to whoever has the fewest active tasks right now. We check if the project already has a developer for that category. If yes, we use the same one for consistency. If no, we use `withCount()` to count each developer's pending and in-progress tasks, then `orderBy()` to get the one with the lowest count. This means fast workers immediately get more tasks instead of sitting idle, and slow workers don't get overloaded."

**Q: "How do you ensure data security?"**
A: "We filter all queries by the logged-in user's ID. Customers only see their own projects and tasks. Developers only see tasks assigned to them. We use Laravel's authentication middleware to protect routes."

**Q: "What happens when a project is deleted?"**
A: "We use soft deletes. The project and all its tasks get a `deleted_at` timestamp but stay in the database. Users can restore them from the Trash page. Only force delete permanently removes them."

**Q: "How do notifications work?"**
A: "We have a `read_at` column in comments and attachments tables. When it's null, the notification is unread. The middleware counts unread items and shares them with all pages. When clicked, we update `read_at` to the current timestamp."

**Q: "Why use Eloquent ORM?"**
A: "It prevents SQL injection automatically, makes relationships easier to work with, and provides cleaner, more readable code. For example, `$task->project->name` instead of writing JOIN queries."

**Q: "Where is the database stored?"**
A: "We use SQLite, so the entire database is stored in a single file at `database/database.sqlite`. It's currently 128 KB and contains all our users, projects, tasks, and comments. SQLite is perfect for development because it doesn't require installing a separate database server. For production, we can easily switch to MySQL by just changing the `.env` configuration - the code stays exactly the same because Laravel's Eloquent ORM is database-agnostic."

**Q: "How do you backup the database?"**
A: "With SQLite, it's as simple as copying the `database/database.sqlite` file. For MySQL in production, we'd use `mysqldump` or Laravel's backup packages. We can also export data using Laravel's seeder system."
