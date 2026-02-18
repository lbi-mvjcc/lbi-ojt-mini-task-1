<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display all tasks
     * - Customers: see all their created tasks
     * - Developers: see their assigned tasks
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isCustomer()) {
            // Get all tasks created by the customer, grouped by unique task (title + project + category)
            $rawTasks = $user->tasksCreated()->with('project', 'assignedTo')->latest()->get();
            
            // Group tasks by unique keys (same task assigned to multiple developers)
            $groupedTasks = $rawTasks->groupBy(function($task) {
                return $task->title . '|' . $task->project_id . '|' . $task->category;
            });
            
            // Transform grouped tasks into display format
            $tasks = $groupedTasks->map(function($taskGroup) {
                $firstTask = $taskGroup->first();
                $developerCount = $taskGroup->count();
                $assignedDevelopers = $taskGroup->pluck('assignedTo.name')->join(', ');
                
                // Calculate status distribution
                $statusCounts = $taskGroup->groupBy('status')->map->count();
                
                return (object) [
                    'id' => $firstTask->id,
                    'title' => $firstTask->title,
                    'description' => $firstTask->description,
                    'category' => $firstTask->category,
                    'project' => $firstTask->project,
                    'created_at' => $firstTask->created_at,
                    'developer_count' => $developerCount,
                    'assigned_developers' => $assignedDevelopers,
                    'status_counts' => $statusCounts,
                    'primary_status' => $this->calculatePrimaryStatus($statusCounts),
                    'all_tasks' => $taskGroup // Keep reference to all task instances
                ];
            });

            return view('tasks.index', compact('tasks'));
        } elseif ($user->isDeveloper()) {
            // Get assigned tasks for developer
            $assignedTasks = $user->tasksAssigned()->latest()->get();
            
            return view('tasks.index', compact('assignedTasks'));
        }

        abort(403, 'Unauthorized');
    }

    /**
     * Calculate primary status based on status distribution
     */
    private function calculatePrimaryStatus($statusCounts)
    {
        if ($statusCounts->get('done', 0) == $statusCounts->sum()) {
            return 'done'; // All completed
        } elseif ($statusCounts->get('in_review', 0) > 0) {
            return 'in_review'; // Some in review
        } elseif ($statusCounts->get('in_progress', 0) > 0) {
            return 'in_progress'; // Some in progress
        } else {
            return 'pending'; // All pending or mixed
        }
    }

    /**
     * Show the form for creating a new task
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only customers can create tasks
        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized');
        }

        // Get existing project names for autocomplete suggestions
        $existingProjects = $user->projects()->pluck('name')->toArray();

        return view('tasks.create', compact('existingProjects'));
    }

    /**
     * Store a newly created task in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only customers can create tasks
        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:frontend,backend,server',
            'deadline' => 'nullable|date|after:today',
            'requires_file_submission' => 'boolean',
            'requires_image_submission' => 'boolean',
            'requires_link_submission' => 'boolean',
            'submission_instructions' => 'nullable|string|max:1000',
        ]);

        // Find or create project for the customer
        $project = Project::firstOrCreate(
            [
                'customer_id' => $user->id,
                'name' => $validated['project_name']
            ],
            [
                'description' => null,
            ]
        );

        try {
            // Automatically assign task to ALL developers in the selected category
            $assignedDevelopers = $this->assignTaskToAllDevelopers($validated['category']);

            // Create individual task for each developer in the category
            $createdTasks = [];
            foreach ($assignedDevelopers as $developer) {
                $task = Task::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'category' => $validated['category'],
                    'project_id' => $project->id,
                    'created_by' => $user->id,
                    'assigned_to' => $developer->id,
                    'status' => 'pending',
                    'deadline' => $validated['deadline'],
                    'requires_file_submission' => $validated['requires_file_submission'] ?? false,
                    'requires_image_submission' => $validated['requires_image_submission'] ?? false,
                    'requires_link_submission' => $validated['requires_link_submission'] ?? false,
                    'submission_instructions' => $validated['submission_instructions'],
                ]);
                $createdTasks[] = $task;
                
                // Create notification for the assigned developer
                Notification::createTaskAssignedNotification($task, $user, [$developer]);
            }

            $developerCount = count($assignedDevelopers);
            $developerNames = $assignedDevelopers->pluck('name')->join(', ', ' and ');
            
            return redirect()->route('tasks.index')->with('success', "Task created successfully and assigned to {$developerCount} {$validated['category']} developer(s): {$developerNames}!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Automatically assign task to ALL developers in the specified category
     */
    private function assignTaskToAllDevelopers($category)
    {
        // Map category to developer role
        $roleMap = [
            'frontend' => User::ROLE_FRONTEND_DEV,
            'backend' => User::ROLE_BACKEND_DEV,
            'server' => User::ROLE_SERVER_ADMIN,
        ];

        $role = $roleMap[$category] ?? User::ROLE_BACKEND_DEV;

        // Get ALL developers of the specified role
        $developers = User::where('role', $role)
            ->orderBy('name')
            ->get();

        if ($developers->isEmpty()) {
            throw new \Exception("No {$category} developers available. Please ensure {$category} developers are registered in the system.");
        }

        return $developers;
    }

    /**
     * Automatically assign task to developer with least workload (legacy method - kept for compatibility)
     */
    private function assignTaskToDeveloper($category)
    {
        // Map category to developer role
        $roleMap = [
            'frontend' => User::ROLE_FRONTEND_DEV,
            'backend' => User::ROLE_BACKEND_DEV,
            'server' => User::ROLE_SERVER_ADMIN,
        ];

        $role = $roleMap[$category] ?? User::ROLE_BACKEND_DEV;

        // Get all developers of the specified role, ordered by workload
        $developers = User::where('role', $role)
            ->withCount(['tasksAssigned' => function($query) {
                // Only count non-completed tasks for better load balancing
                $query->whereIn('status', ['pending', 'in_progress', 'in_review']);
            }])
            ->orderBy('tasks_assigned_count', 'asc')
            ->orderBy('created_at', 'asc') // Secondary sort by registration date for fairness
            ->get();

        if ($developers->isEmpty()) {
            // Fallback: if no developers of the specified role, get any developer
            $fallbackDeveloper = User::where('role', 'like', '%_dev')
                ->orWhere('role', User::ROLE_SERVER_ADMIN)
                ->withCount(['tasksAssigned' => function($query) {
                    $query->whereIn('status', ['pending', 'in_progress', 'in_review']);
                }])
                ->orderBy('tasks_assigned_count', 'asc')
                ->first();
            
            if (!$fallbackDeveloper) {
                throw new \Exception("No developers available to assign tasks. Please register developers first.");
            }
            
            return $fallbackDeveloper;
        }

        // Among developers with the same workload, use round-robin by picking randomly
        $minWorkload = $developers->first()->tasks_assigned_count;
        $availableDevelopers = $developers->where('tasks_assigned_count', $minWorkload);
        
        if ($availableDevelopers->count() > 1) {
            // Use session-based round-robin for fair distribution
            $sessionKey = "last_assigned_{$role}";
            $lastAssignedId = session($sessionKey, 0);
            
            // Find next developer after the last assigned one
            $developersArray = $availableDevelopers->toArray();
            $currentIndex = array_search($lastAssignedId, array_column($developersArray, 'id'));
            $nextIndex = ($currentIndex === false) ? 0 : (($currentIndex + 1) % count($developersArray));
            
            $selectedDeveloper = $availableDevelopers->values()[$nextIndex];
            session([$sessionKey => $selectedDeveloper->id]);
            
            return $selectedDeveloper;
        }

        return $developers->first();
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $user = Auth::user();
        
        // Check authorization - allow if customer who created it or developer assigned to it
        if ($user->isCustomer()) {
            if ($task->project->customer_id !== $user->id) {
                abort(403, 'Unauthorized');
            }
            
            // For customers, get all instances of this task (all developers)
            $allTaskInstances = Task::where('title', $task->title)
                ->where('category', $task->category)
                ->where('project_id', $task->project_id)
                ->where('created_by', $user->id)
                ->with('assignedTo')
                ->get();
            
            // Load submissions for this task
            $task->load('submissions.user');
                
            return view('tasks.show', compact('task', 'allTaskInstances'));
        } elseif ($user->isDeveloper()) {
            if ($task->assigned_to !== $user->id) {
                abort(403, 'Unauthorized');
            }
            
            // Load submissions for this task
            $task->load('submissions.user');
            
            return view('tasks.show', compact('task'));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Show the form for editing the specified task
     */
    public function edit(Task $task)
    {
        $user = Auth::user();
        
        // Only customers can edit tasks they created
        if (!$user->isCustomer() || $task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Get count of developers assigned to this task group
        $developerCount = Task::where('title', $task->title)
            ->where('category', $task->category)
            ->where('project_id', $task->project_id)
            ->where('created_by', $user->id)
            ->count();

        return view('tasks.edit', compact('task', 'developerCount'));
    }

    /**
     * Update the specified task in storage
     */
    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Only customers can update their tasks
        if (!$user->isCustomer() || $task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:frontend,backend,server',
            'deadline' => 'nullable|date|after:today',
        ]);

        try {
            // If category changed, we need to delete old tasks and create new ones
            if ($task->category !== $validated['category']) {
                // Delete all instances of the old task
                Task::where('title', $task->title)
                    ->where('category', $task->category)
                    ->where('project_id', $task->project_id)
                    ->where('created_by', $user->id)
                    ->delete();
                
                // Get all developers in the new category
                $newDevelopers = $this->assignTaskToAllDevelopers($validated['category']);
                
                // Create tasks for all developers in the new category
                foreach ($newDevelopers as $developer) {
                    $newTask = Task::create([
                        'title' => $validated['title'],
                        'description' => $validated['description'],
                        'category' => $validated['category'],
                        'project_id' => $task->project_id,
                        'created_by' => $user->id,
                        'assigned_to' => $developer->id,
                        'status' => 'pending',
                        'deadline' => $validated['deadline'],
                    ]);
                    
                    // Create notification for the newly assigned developer
                    Notification::createTaskAssignedNotification($newTask, $user, [$developer]);
                }
                
                $developerCount = count($newDevelopers);
                $developerNames = $newDevelopers->pluck('name')->join(', ', ' and ');
                
                return redirect()->route('tasks.index')->with('success', "Task updated and reassigned to {$developerCount} {$validated['category']} developer(s): {$developerNames}!");
            } else {
                // If category didn't change, update ALL instances of this task
                $tasksToUpdate = Task::where('title', $task->title)
                    ->where('category', $task->category)
                    ->where('project_id', $task->project_id)
                    ->where('created_by', $user->id)
                    ->with('assignedTo')
                    ->get();
                
                $updatedCount = $tasksToUpdate->count();
                
                // Update all task instances
                foreach ($tasksToUpdate as $taskInstance) {
                    $taskInstance->update([
                        'title' => $validated['title'],
                        'description' => $validated['description'],
                        'deadline' => $validated['deadline'],
                    ]);
                    
                    // Create notification for each assigned developer
                    if ($taskInstance->assignedTo) {
                        Notification::createTaskUpdatedNotification($taskInstance, $user, [$taskInstance->assignedTo]);
                    }
                }
                
                return redirect()->route('tasks.index')->with('success', "Task updated successfully for {$updatedCount} developer(s)!");
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage
     */
    public function destroy(Task $task)
    {
        $user = Auth::user();
        
        // Only customers can delete their tasks
        if (!$user->isCustomer() || $task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Get all tasks that will be deleted to notify assigned developers
        $tasksToDelete = Task::where('title', $task->title)
            ->where('category', $task->category)  
            ->where('project_id', $task->project_id)
            ->where('created_by', $user->id)
            ->with('assignedTo')
            ->get();
        
        // Create notifications for assigned developers before deleting
        foreach ($tasksToDelete as $taskInstance) {
            if ($taskInstance->assignedTo) {
                Notification::createTaskDeletedNotification($taskInstance, $user, [$taskInstance->assignedTo]);
            }
        }

        // Delete all tasks with the same title, category, and project (all developer instances)
        $deletedCount = $tasksToDelete->count();
        Task::where('title', $task->title)
            ->where('category', $task->category)  
            ->where('project_id', $task->project_id)
            ->where('created_by', $user->id)
            ->delete();

        return redirect()->route('tasks.index')->with('success', "Task deleted successfully! Removed from {$deletedCount} developer(s).");
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Only developers assigned to the task can update status
        if (!$user->isDeveloper() || $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,in_review,done',
        ]);

        $oldStatus = $task->status;
        $newStatus = $validated['status'];
        
        $task->update(['status' => $newStatus]);

        // Create notification for the customer who created the task
        if ($oldStatus !== $newStatus) {
            $customer = $task->createdBy;
            Notification::createStatusUpdatedNotification($task, $user, $customer, $oldStatus, $newStatus);
        }

        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully!');
    }

    /**
     * Display developer workload dashboard (for monitoring task distribution)
     */
    public function developerWorkload()
    {
        $user = Auth::user();
        
        // Only allow authorized users to view this
        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized');
        }

        // Get all developers with their task counts
        $developers = User::where('role', 'like', '%_dev')
            ->orWhere('role', User::ROLE_SERVER_ADMIN)
            ->withCount([
                'tasksAssigned',
                'tasksAssigned as pending_tasks_count' => function($query) {
                    $query->where('status', 'pending');
                },
                'tasksAssigned as in_progress_tasks_count' => function($query) {
                    $query->where('status', 'in_progress');
                },
                'tasksAssigned as in_review_tasks_count' => function($query) {
                    $query->where('status', 'in_review');
                },
                'tasksAssigned as completed_tasks_count' => function($query) {
                    $query->where('status', 'done');
                }
            ])
            ->orderBy('role')
            ->orderBy('tasks_assigned_count')
            ->get()
            ->groupBy('role');

        // Calculate total status counts for the summary
        $totalPending = $developers->flatten()->sum('pending_tasks_count');
        $totalInProgress = $developers->flatten()->sum('in_progress_tasks_count');
        $totalInReview = $developers->flatten()->sum('in_review_tasks_count');
        $totalCompleted = $developers->flatten()->sum('completed_tasks_count');

        return view('tasks.developer-workload', compact('developers', 'totalPending', 'totalInProgress', 'totalInReview', 'totalCompleted'));
    }

    /**
     * Display customer's projects with tasks
     */
    public function projects()
    {
        $user = Auth::user();
        
        // Only customers can view their projects
        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized');
        }

        // Get customer's projects that have tasks
        $projects = $user->projects()->has('tasks')->with('tasks')->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Search for tasks and projects based on user role
     */
    public function search(Request $request)
    {
        $query = $request->input('search');
        $user = Auth::user();
        
        if (empty($query)) {
            return back()->with('error', 'Please enter a search term.');
        }

        $searchResults = [
            'tasks' => collect([]),
            'projects' => collect([]),
            'query' => $query
        ];

        if ($user->isCustomer()) {
            // Customer: Search their created tasks and their projects
            $rawTasks = $user->tasksCreated()
                ->with('project', 'assignedTo')
                ->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('category', 'LIKE', "%{$query}%")
                      ->orWhereHas('project', function($projectQuery) use ($query) {
                          $projectQuery->where('name', 'LIKE', "%{$query}%");
                      });
                })
                ->latest()
                ->get();

            // Group tasks like in index method
            $groupedTasks = $rawTasks->groupBy(function($task) {
                return $task->title . '|' . $task->project_id . '|' . $task->category;
            });

            $searchResults['tasks'] = $groupedTasks->map(function($taskGroup) {
                $firstTask = $taskGroup->first();
                $developerCount = $taskGroup->count();
                $assignedDevelopers = $taskGroup->pluck('assignedTo.name')->join(', ');
                
                $statusCounts = $taskGroup->groupBy('status')->map->count();
                
                return (object) [
                    'id' => $firstTask->id,
                    'title' => $firstTask->title,
                    'description' => $firstTask->description,
                    'category' => $firstTask->category,
                    'project' => $firstTask->project,
                    'created_at' => $firstTask->created_at,
                    'developer_count' => $developerCount,
                    'assigned_developers' => $assignedDevelopers,
                    'status_counts' => $statusCounts,
                    'primary_status' => $this->calculatePrimaryStatus($statusCounts),
                    'all_tasks' => $taskGroup
                ];
            });

            // Search customer's projects
            $searchResults['projects'] = $user->projects()
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->has('tasks')
                ->with('tasks')
                ->get();

        } elseif ($user->isDeveloper()) {
            // Developer: Search their assigned tasks
            $searchResults['tasks'] = $user->tasksAssigned()
                ->with('project', 'createdBy')
                ->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('category', 'LIKE', "%{$query}%")
                      ->orWhereHas('project', function($projectQuery) use ($query) {
                          $projectQuery->where('name', 'LIKE', "%{$query}%");
                      });
                })
                ->latest()
                ->get();

            // Also search projects related to their assigned tasks
            $projectIds = $searchResults['tasks']->pluck('project_id')->unique();
            $searchResults['projects'] = Project::whereIn('id', $projectIds)
                ->orWhere(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->whereHas('tasks', function($taskQuery) use ($user) {
                    $taskQuery->where('assigned_to', $user->id);
                })
                ->with('tasks')
                ->get();
        }

        return view('tasks.search-results', $searchResults);
    }
}

