<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Notification;
use App\Models\PasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized - Admin access only');
        }

        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_developers' => User::whereIn('role', ['frontend_dev', 'backend_dev', 'server_admin'])->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'in_progress_tasks' => Task::where('status', 'in_progress')->count(),
            'completed_tasks' => Task::where('status', 'done')->count(),
            'total_projects' => Project::count(),
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::whereNull('read_at')->count(),
        ];

        // Get recent activities
        $recent_users = User::latest()->take(5)->get();
        $recent_tasks = Task::with('createdBy', 'assignedTo', 'project')->latest()->take(10)->get();
        $recent_projects = Project::with('customer')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_tasks', 'recent_projects'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $users = User::withCount(['tasksCreated', 'tasksAssigned'])->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Show form to create new user
     */
    public function createUser()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('admin.create-user');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,customer,frontend_dev,backend_dev,server_admin'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    /**
     * Show form to edit user
     */
    public function editUser(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('admin.edit-user', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,customer,frontend_dev,backend_dev,server_admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }

    /**
     * Display all tasks
     */
    public function tasks()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $tasks = Task::with('createdBy', 'assignedTo', 'project')->latest()->paginate(20);
        return view('admin.tasks', compact('tasks'));
    }

    /**
     * Display all projects
     */
    public function projects()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $projects = Project::with('customer')->withCount('tasks')->latest()->paginate(20);
        return view('admin.projects', compact('projects'));
    }

    /**
     * Delete task
     */
    public function deleteTask(Task $task)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    /**
     * Delete project
     */
    public function deleteProject(Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully!');
    }

    /**
     * Show password reset codes page
     */
    public function passwordResetCodes()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized - Admin access only');
        }

        $codes = PasswordResetCode::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.password-reset-codes', compact('codes'));
    }

    /**
     * Generate password reset code for a user
     */
    public function generateResetCode(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized - Admin access only');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Invalidate any existing codes for this user
        PasswordResetCode::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true]);

        // Generate a 6-digit code
        $code = strtoupper(Str::random(6));

        // Create new reset code (valid for 24 hours)
        $resetCode = PasswordResetCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addHours(24),
            'used' => false,
        ]);

        return redirect()->back()->with('success', "Reset code generated for {$user->name}: {$code}");
    }

    /**
     * Show deleted (trashed) items
     */
    public function trash()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $deletedUsers = User::onlyTrashed()->get();
        $deletedProjects = Project::onlyTrashed()->with('customer')->get();

        return view('admin.trash', compact('deletedUsers', 'deletedProjects'));
    }

    /**
     * Restore deleted user
     */
    public function restoreUser($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', 'User restored successfully!');
    }

    /**
     * Restore deleted project
     */
    public function restoreProject($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return redirect()->back()->with('success', 'Project restored successfully!');
    }

    /**
     * Permanently delete user
     */
    public function forceDeleteUser($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->back()->with('success', 'User permanently deleted!');
    }

    /**
     * Permanently delete project
     */
    public function forceDeleteProject($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $project = Project::onlyTrashed()->findOrFail($id);
        $project->forceDelete();

        return redirect()->back()->with('success', 'Project permanently deleted!');
    }
}
