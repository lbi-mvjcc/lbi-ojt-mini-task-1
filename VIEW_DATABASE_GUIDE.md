# How to View Your Database Data

## Method 1: Using Laravel Tinker (Quick & Easy)

### Step 1: Open Tinker
```bash
php artisan tinker
```

### Step 2: View Your Data

**See all users:**
```php
User::all();
```

**See users in a nice table format:**
```php
User::all(['id', 'name', 'email', 'role']);
```

**Count users by role:**
```php
echo "Customers: " . User::where('role', 'customer')->count() . "\n";
echo "Frontend Devs: " . User::where('role', 'frontend_developer')->count() . "\n";
echo "Backend Devs: " . User::where('role', 'backend_developer')->count() . "\n";
echo "Server Admins: " . User::where('role', 'server_administrator')->count() . "\n";
```

**See all projects:**
```php
Project::with('customer')->get(['id', 'name', 'customer_id']);
```

**See all tasks:**
```php
Task::with(['project', 'assignedUser'])->get(['id', 'title', 'category', 'status', 'assigned_to']);
```

**See tasks assigned to Frontend Developer 1:**
```php
$dev = User::where('email', 'frontend1@example.com')->first();
Task::where('assigned_to', $dev->id)->with('project')->get(['title', 'status', 'project_id']);
```

**See a specific user's details:**
```php
User::find(1);
```

**Exit Tinker:**
```php
exit
```

---

## Method 2: Using DB Browser for SQLite (Visual GUI)

### Step 1: Download
1. Go to: https://sqlitebrowser.org/dl/
2. Download "DB Browser for SQLite" for Windows
3. Install it (just click Next, Next, Install)

### Step 2: Open Your Database
1. Open DB Browser for SQLite
2. Click "Open Database"
3. Navigate to your project folder
4. Select: `database/database.sqlite`

### Step 3: Browse Data
- Click "Browse Data" tab
- Select table from dropdown (users, projects, tasks, etc.)
- You'll see all records in a nice table format
- You can sort, filter, and search

### Step 4: Run SQL Queries
- Click "Execute SQL" tab
- Type queries like:
```sql
SELECT * FROM users;
SELECT * FROM tasks WHERE status = 'pending';
SELECT p.name, COUNT(t.id) as task_count 
FROM projects p 
LEFT JOIN tasks t ON p.id = t.project_id 
GROUP BY p.id;
```

---

## Quick Commands for Your Presentation

### Show System Overview
```bash
php artisan tinker
```

```php
// Total counts
echo "=== SYSTEM OVERVIEW ===\n";
echo "Total Users: " . User::count() . "\n";
echo "Total Projects: " . Project::count() . "\n";
echo "Total Tasks: " . Task::count() . "\n\n";

// User breakdown
echo "=== USERS BY ROLE ===\n";
echo "Customers: " . User::where('role', 'customer')->count() . "\n";
echo "Frontend Developers: " . User::where('role', 'frontend_developer')->count() . "\n";
echo "Backend Developers: " . User::where('role', 'backend_developer')->count() . "\n";
echo "Server Administrators: " . User::where('role', 'server_administrator')->count() . "\n\n";

// Task breakdown
echo "=== TASKS BY STATUS ===\n";
echo "Pending: " . Task::where('status', 'pending')->count() . "\n";
echo "In Progress: " . Task::where('status', 'in_progress')->count() . "\n";
echo "Completed: " . Task::where('status', 'completed')->count() . "\n\n";

// Task breakdown by category
echo "=== TASKS BY CATEGORY ===\n";
echo "Frontend: " . Task::where('category', 'frontend')->count() . "\n";
echo "Backend: " . Task::where('category', 'backend')->count() . "\n";
echo "Server: " . Task::where('category', 'server')->count() . "\n";
```

### Show Developer Workload
```php
echo "=== FRONTEND DEVELOPER WORKLOAD ===\n";
User::where('role', 'frontend_developer')
    ->withCount(['assignedTasks' => function($q) {
        $q->whereIn('status', ['pending', 'in_progress']);
    }])
    ->get()
    ->each(function($u) {
        echo $u->name . ": " . $u->assigned_tasks_count . " active tasks\n";
    });
```

### Show Project Details
```php
Project::with('customer')->get()->each(function($p) {
    echo "\nProject: " . $p->name . "\n";
    echo "Customer: " . $p->customer->name . "\n";
    echo "Tasks: " . $p->tasks()->count() . "\n";
});
```

---

## Tips for Presentation

1. **Before presenting, prepare a script:**
   - Save common queries in a text file
   - Copy-paste them during demo to avoid typos

2. **Show the smart assignment in action:**
   ```php
   // Show current workload
   User::where('role', 'frontend_developer')
       ->withCount(['assignedTasks' => function($q) {
           $q->whereIn('status', ['pending', 'in_progress']);
       }])
       ->orderBy('assigned_tasks_count', 'asc')
       ->get(['name', 'assigned_tasks_count']);
   ```

3. **Demonstrate data relationships:**
   ```php
   $task = Task::with(['project', 'customer', 'assignedUser'])->first();
   echo "Task: " . $task->title . "\n";
   echo "Project: " . $task->project->name . "\n";
   echo "Customer: " . $task->customer->name . "\n";
   echo "Assigned to: " . $task->assignedUser->name . "\n";
   ```

---

## Common Issues

**Issue: "Class 'User' not found"**
Solution: Use full namespace:
```php
\App\Models\User::all();
```

**Issue: Tinker shows too much data**
Solution: Limit results:
```php
User::take(5)->get();  // Only first 5
```

**Issue: Want to see specific columns only**
Solution:
```php
User::all(['id', 'name', 'email']);
```
