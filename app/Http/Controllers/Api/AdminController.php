<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Dashboard Statistics
    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'customers' => User::where('role', 'customer')->count(),
            'developers' => User::whereIn('role', ['frontend_developer', 'backend_developer', 'server_admin'])->count(),
            'active_projects' => Project::where('is_active', true)->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
        ]);
    }

    // User Management
    public function getUsers(Request $request)
    {
        $currentAdminId = $request->user()->id;
        
        // Get all users and sort them so current admin is first
        $users = User::orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END", [$currentAdminId])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:customer,frontend_developer,backend_developer,server_admin,admin'],
            'projects' => ['nullable', 'array'], // For customers: array of project IDs
            'projects.*' => ['exists:projects,id'],
        ]);

        // Check if assigning projects that already have customers
        if ($validated['role'] === 'customer' && !empty($validated['projects'])) {
            $projectsWithCustomers = Project::whereIn('id', $validated['projects'])
                ->whereNotNull('customer_id')
                ->pluck('name')
                ->toArray();
            
            if (!empty($projectsWithCustomers)) {
                return response()->json([
                    'message' => 'The following projects already have customers assigned: ' . implode(', ', $projectsWithCustomers) . '. Only one customer per project is allowed.'
                ], 422);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Assign projects to customer
        if ($validated['role'] === 'customer' && !empty($validated['projects'])) {
            Project::whereIn('id', $validated['projects'])->update(['customer_id' => $user->id]);
        }

        return response()->json($user, 201);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:customer,frontend_developer,backend_developer,server_admin,admin'],
            'projects' => ['nullable', 'array'], // For customers: array of project IDs
            'projects.*' => ['exists:projects,id'],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
        ]);

        // Check if assigning projects that already have other customers
        if ($validated['role'] === 'customer' && !empty($validated['projects'])) {
            $projectsWithOtherCustomers = Project::whereIn('id', $validated['projects'])
                ->whereNotNull('customer_id')
                ->where('customer_id', '!=', $user->id)
                ->pluck('name')
                ->toArray();
            
            if (!empty($projectsWithOtherCustomers)) {
                return response()->json([
                    'message' => 'The following projects already have customers assigned: ' . implode(', ', $projectsWithOtherCustomers) . '. Only one customer per project is allowed.'
                ], 422);
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                $oldPath = storage_path('app/public/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile_pictures', $filename, 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        // Update project assignments for customers
        if ($validated['role'] === 'customer') {
            // Remove this customer from all projects first
            Project::where('customer_id', $user->id)->update(['customer_id' => null]);
            
            // Assign new projects
            if (!empty($validated['projects'])) {
                Project::whereIn('id', $validated['projects'])->update(['customer_id' => $user->id]);
            }
        }

        return response()->json($user);
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // Project Management
    public function getProjects()
    {
        $projects = Project::withCount(['tasks', 'members'])->latest()->get();
        return response()->json($projects);
    }

    public function createProject(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    public function updateProject(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    // Project Members Management
    public function getProjectMembers(Project $project)
    {
        $members = $project->members()->withPivot('role')->get();
        return response()->json($members);
    }

    public function addProjectMember(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['required', 'in:frontend_developer,backend_developer,server_admin'],
        ]);

        // Check if project already has 3 members (maximum limit)
        $currentMemberCount = $project->members()->count();
        if ($currentMemberCount >= 3) {
            return response()->json([
                'message' => 'This project already has the maximum of 3 developers. Each project can only have 1 Frontend Developer, 1 Backend Developer, and 1 Server Admin.'
            ], 422);
        }

        // Check if user is already a member
        if ($project->members()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json(['message' => 'User is already a member of this project'], 422);
        }

        // Check if role is already taken
        if ($project->members()->wherePivot('role', $validated['role'])->exists()) {
            return response()->json([
                'message' => 'This role is already assigned to another developer in this project'
            ], 422);
        }

        $project->members()->attach($validated['user_id'], ['role' => $validated['role']]);
        
        return response()->json(['message' => 'Member added successfully']);
    }

    public function removeProjectMember(Project $project, User $user)
    {
        $project->members()->detach($user->id);
        return response()->json(['message' => 'Member removed successfully']);
    }

    // All Tasks Overview
    public function getAllTasks()
    {
        $tasks = Task::with(['project', 'customer', 'assignedDeveloper'])
            ->latest()
            ->get();
        
        return response()->json($tasks);
    }

    // Recently Deleted - Users
    public function getTrashedUsers()
    {
        $users = User::onlyTrashed()
            ->latest('deleted_at')
            ->get();
            
        return response()->json($users);
    }

    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        
        return response()->json(['message' => 'User restored successfully', 'user' => $user]);
    }

    public function forceDeleteUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        
        return response()->json(['message' => 'User permanently deleted']);
    }

    // Recently Deleted - Projects
    public function getTrashedProjects()
    {
        $projects = Project::onlyTrashed()
            ->withCount(['tasks', 'members'])
            ->latest('deleted_at')
            ->get();
            
        return response()->json($projects);
    }

    public function restoreProject($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();
        
        return response()->json(['message' => 'Project restored successfully', 'project' => $project]);
    }

    public function forceDeleteProject($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        
        // Force delete all associated tasks
        Task::onlyTrashed()->where('project_id', $id)->forceDelete();
        
        $project->forceDelete();
        
        return response()->json(['message' => 'Project permanently deleted']);
    }
}
