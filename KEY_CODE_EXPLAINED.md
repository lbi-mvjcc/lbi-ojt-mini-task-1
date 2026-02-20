# Key Code Explained - The Magic Behind Each Feature

## 1. LEAST-LOADED DISTRIBUTION ALGORITHM

**The 3 lines that make it work:**

```php
User::where('role', $role)                    // 1. Find developers with matching role
    ->withCount(['assignedTasks' => function($query) {
        $query->whereIn('status', ['pending', 'in_progress']);  // 2. Count only active tasks
    }])
    ->orderBy('assigned_tasks_count', 'asc')  // 3. Sort by count (lowest first)
    ->first();                                 // 4. Pick the first one (least busy)
```

**Why this works:**
- `withCount()` - Adds a count of related records (tasks) to each user
- `whereIn('status', [...])` - Only counts pending/in_progress (not completed)
- `orderBy('asc')` - Sorts from lowest to highest
- `first()` - Gets the developer with the LEAST tasks

**Without this, you'd need:**
```php
// BAD: Manual counting (slow and inefficient)
$developers = User::where('role', $role)->get();
$leastBusy = null;
$minTasks = PHP_INT_MAX;

foreach ($developers as $dev) {
    $taskCount = Task::where('assigned_to', $dev->id)
        ->whereIn('status', ['pending', 'in_progress'])
        ->count();
    
    if ($taskCount < $minTasks) {
        $minTasks = $taskCount;
        $leastBusy = $dev;
    }
}
```

---

## 2. DATA ISOLATION (Security)

**The 1 line that protects your data:**

```php
Task::where('customer_id', $user->id)->get();  // Only tasks belonging to this user
```

**Why this works:**
- `where('customer_id', $user->id)` - Filters by logged-in user's ID
- Without this, users could see ALL tasks in the database

**Example:**
```php
// INSECURE - Shows ALL tasks
Task::all();  // ❌ BAD

// SECURE - Shows only user's tasks
Task::where('customer_id', auth()->id())->get();  // ✅ GOOD
```

---

## 3. ELOQUENT RELATIONSHIPS

**The code that connects tables:**

```php
// In Task model
public function project()
{
    return $this->belongsTo(Project::class);
}
```

**Why this works:**
- Now you can do: `$task->project->name`
- Laravel automatically joins the tables
- No need to write SQL JOIN queries

**Without relationships:**
```php
// BAD: Manual joining
$task = Task::find(1);
$project = Project::find($task->project_id);  // Extra query!
echo $project->name;
```

**With relationships:**
```php
// GOOD: Automatic joining
$task = Task::with('project')->find(1);  // One query with JOIN
echo $task->project->name;
```

---

## 4. SOFT DELETES

**The 1 line that enables trash/restore:**

```php
use SoftDeletes;  // In your model
```

**Why this works:**
- Instead of deleting records, Laravel sets `deleted_at` timestamp
- `delete()` - Sets timestamp (soft delete)
- `forceDelete()` - Actually removes from database
- `restore()` - Sets timestamp back to null

**What happens:**
```php
$project->delete();           // Sets deleted_at = '2026-02-19 10:30:00'
Project::all();               // Doesn't include deleted projects
Project::onlyTrashed()->get(); // Shows only deleted projects
$project->restore();          // Sets deleted_at = null
```

---

## 5. EAGER LOADING (Prevents N+1 Problem)

**The code that makes queries fast:**

```php
Task::with(['project', 'customer'])->get();  // 1 query for tasks + 1 for projects + 1 for customers = 3 queries
```

**Without eager loading (N+1 problem):**
```php
$tasks = Task::all();  // 1 query

foreach ($tasks as $task) {
    echo $task->project->name;  // 1 query PER task!
}
// If you have 100 tasks = 101 queries! 😱
```

**With eager loading:**
```php
$tasks = Task::with('project')->get();  // 2 queries total

foreach ($tasks as $task) {
    echo $task->project->name;  // No extra queries!
}
// Always just 2 queries, no matter how many tasks! 🎉
```

---

## 6. VALIDATION

**The code that prevents bad data:**

```php
$request->validate([
    'title' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
    'file' => 'required|file|max:51200',  // 50MB in KB
]);
```

**Why this works:**
- `required` - Field must be present
- `email` - Must be valid email format
- `unique:users` - Checks if email already exists in users table
- `max:51200` - Maximum size in kilobytes

**What happens if validation fails:**
- Laravel automatically redirects back
- Shows error messages
- Old input is preserved
- No database changes are made

---

## 7. MIDDLEWARE (Sharing Data with All Pages)

**The code that makes notifications work:**

```php
// In HandleInertiaRequests middleware
public function share(Request $request): array
{
    return [
        'auth' => ['user' => $request->user()],
        'notifications' => $this->getNotifications(),
    ];
}
```

**Why this works:**
- Middleware runs on EVERY request
- Data returned here is available on ALL pages
- No need to pass notifications from every controller

**Usage in Vue:**
```javascript
const page = usePage();
const notifications = page.props.notifications;  // Available everywhere!
```

---

## 8. QUERY SCOPES (Reusable Filters)

**The code that makes filtering easy:**

```php
// Instead of repeating this everywhere:
Task::where('status', 'pending')->where('assigned_to', $userId)->get();

// Create a scope in Task model:
public function scopeActive($query)
{
    return $query->whereIn('status', ['pending', 'in_progress']);
}

// Now use it anywhere:
Task::active()->get();
Task::active()->where('assigned_to', $userId)->get();
```

---

## 9. FORM DATA HANDLING (Inertia.js)

**The code that submits forms:**

```javascript
// In Vue component
const form = useForm({
    title: '',
    description: '',
});

form.post('/tasks', {
    onSuccess: () => {
        form.reset();  // Clear form
    },
});
```

**Why this works:**
- `useForm()` - Creates reactive form with validation errors
- `form.post()` - Sends data to Laravel
- `onSuccess` - Runs after successful submission
- Errors automatically populate `form.errors`

---

## 10. AUTHORIZATION (Checking Permissions)

**The code that controls access:**

```php
// Check if user owns the resource
if ($project->customer_id !== auth()->id()) {
    abort(403, 'Unauthorized');
}

// Or use helper methods
if (!$user->isCustomer()) {
    abort(403, 'Only customers can create projects');
}
```

**Why this works:**
- `abort(403)` - Returns "Forbidden" error
- Stops execution immediately
- Prevents unauthorized actions

---

## KEY TAKEAWAYS

### 1. Least-Loaded Algorithm = `withCount()` + `orderBy()` + `first()`
```php
User::withCount('tasks')->orderBy('tasks_count', 'asc')->first();
```

### 2. Data Isolation = `where('user_id', auth()->id())`
```php
Task::where('customer_id', auth()->id())->get();
```

### 3. Relationships = `belongsTo()` / `hasMany()`
```php
public function project() { return $this->belongsTo(Project::class); }
```

### 4. Soft Deletes = `use SoftDeletes;`
```php
$model->delete();  // Soft delete
$model->restore(); // Restore
```

### 5. Eager Loading = `with()`
```php
Task::with(['project', 'customer'])->get();
```

### 6. Validation = `$request->validate()`
```php
$request->validate(['email' => 'required|email|unique:users']);
```

---

## PRACTICE QUESTIONS FOR PRESENTATION

**Q: "How does your load balancing work?"**
A: "We use `withCount()` to count each developer's active tasks, then `orderBy()` to sort by count, and `first()` to pick the least busy one. It's efficient because it's done in a single database query."

**Q: "How do you prevent users from seeing other people's data?"**
A: "Every query filters by the logged-in user's ID using `where('customer_id', auth()->id())`. This ensures users only see their own data."

**Q: "What's the difference between delete and soft delete?"**
A: "Regular delete removes the record from the database. Soft delete just sets a `deleted_at` timestamp, so we can restore it later from the trash. We use Laravel's `SoftDeletes` trait for this."

**Q: "How do you handle file uploads?"**
A: "We validate the file type and size using Laravel's validation rules. Files are stored in `storage/app/public` and we save the path in the database. We also check file size client-side before uploading to give immediate feedback."

**Q: "What prevents SQL injection?"**
A: "Laravel's Eloquent ORM uses prepared statements automatically. When we do `where('id', $id)`, Laravel binds the parameter safely, preventing SQL injection."
