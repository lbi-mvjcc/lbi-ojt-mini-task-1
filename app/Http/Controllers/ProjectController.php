<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            // Customers only see projects they created
            $projects = Project::where('customer_id', $user->id)
                ->withCount(['tasks as customer_tasks_count' => function ($query) use ($user) {
                    $query->where('customer_id', $user->id);
                }])
                ->get();
        } else {
            // Developers see projects where they have assigned tasks
            $projects = Project::whereHas('tasks', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->with(['customer'])
            ->withCount(['tasks as assigned_tasks_count' => function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            }])
            ->get();
        }

        return Inertia::render('Projects', [
            'projects' => $projects,
        ]);
    }

    public function show(Project $project)
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            // Customers can only see their own projects
            if ($project->customer_id !== $user->id) {
                abort(403, 'You do not have access to this project');
            }
            
            // Customers only see their own tasks in this project
            $project->load(['tasks' => function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                    ->with('project')
                    ->latest();
            }]);
        } else {
            // Developers see only their assigned tasks in this project
            $hasTasksInProject = $project->tasks()->where('assigned_to', $user->id)->exists();
            
            if (!$hasTasksInProject) {
                abort(403, 'You are not assigned to this project');
            }
            
            // Load only tasks assigned to this developer
            $project->load(['tasks' => function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->latest();
            }, 'customer']);
        }

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    public function create()
    {
        // Only customers can create projects
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can create projects');
        }

        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        // Only customers can create projects
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can create projects');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'customer_id' => auth()->id(),
        ]);

        return redirect()->route('tasks.create', ['project' => $project->id])
            ->with('success', 'Project created successfully! Now create your first task.');
    }

    public function update(Request $request, Project $project)
    {
        // Only customers can update projects
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can update projects');
        }

        // Check if the customer owns this project
        if ($project->customer_id !== auth()->id()) {
            abort(403, 'You do not have permission to update this project');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project->update($validated);

        return back()->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        // Only customers can delete projects
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can delete projects');
        }

        // Check if the customer owns this project
        if ($project->customer_id !== auth()->id()) {
            abort(403, 'You do not have permission to delete this project');
        }

        // Soft delete all tasks associated with this project first
        $project->tasks()->delete();
        
        // Then soft delete the project
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project moved to trash. You can restore it from the Trash page.');
    }

    public function trash()
    {
        // Only customers can view trash
        if (!auth()->user()->isCustomer()) {
            abort(403);
        }

        $trashedProjects = Project::onlyTrashed()
            ->where('customer_id', auth()->id())
            ->withCount(['tasks' => function ($query) {
                $query->withTrashed();
            }])
            ->get();

        $trashedTasks = Task::onlyTrashed()
            ->where('customer_id', auth()->id())
            ->with(['project'])
            ->get();

        return Inertia::render('Projects/Trash', [
            'projects' => $trashedProjects,
            'tasks' => $trashedTasks,
        ]);
    }

    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        // Only customers can restore projects
        if (!auth()->user()->isCustomer()) {
            abort(403);
        }

        // Check if the customer owns this project
        if ($project->customer_id !== auth()->id()) {
            abort(403);
        }

        // Restore all tasks associated with this project first
        Task::onlyTrashed()->where('project_id', $project->id)->restore();
        
        // Then restore the project
        $project->restore();

        return redirect()->route('projects.trash')
            ->with('success', 'Project restored successfully!');
    }

    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        // Only customers can permanently delete projects
        if (!auth()->user()->isCustomer()) {
            abort(403);
        }

        // Check if the customer owns this project
        if ($project->customer_id !== auth()->id()) {
            abort(403);
        }

        // Permanently delete all tasks
        Task::onlyTrashed()->where('project_id', $project->id)->forceDelete();
        
        // Permanently delete the project
        $project->forceDelete();

        return redirect()->route('projects.trash')
            ->with('success', 'Project permanently deleted!');
    }
}
