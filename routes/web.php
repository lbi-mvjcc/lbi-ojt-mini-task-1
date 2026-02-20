<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            // Customer dashboard - show their created tasks
            $tasks = \App\Models\Task::where('customer_id', $user->id)
                ->with('project')
                ->latest()
                ->take(5)
                ->get();
            
            $projects = \App\Models\Project::where('customer_id', $user->id)->get();
            
            return Inertia::render('Dashboard', [
                'recentTasks' => $tasks,
                'projects' => $projects,
                'stats' => [
                    'total_tasks' => \App\Models\Task::where('customer_id', $user->id)->count(),
                    'pending' => \App\Models\Task::where('customer_id', $user->id)->where('status', 'pending')->count(),
                    'in_progress' => \App\Models\Task::where('customer_id', $user->id)->where('status', 'in_progress')->count(),
                    'completed' => \App\Models\Task::where('customer_id', $user->id)->where('status', 'completed')->count(),
                ]
            ]);
        } else {
            // Developer dashboard - show assigned tasks
            $tasks = \App\Models\Task::where('assigned_to', $user->id)
                ->with(['project', 'customer'])
                ->latest()
                ->take(5)
                ->get();
            
            // Get projects where developer has assigned tasks
            $projects = \App\Models\Project::whereHas('tasks', function($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->with('customer')
            ->get();
            
            return Inertia::render('Dashboard', [
                'assignedTasks' => $tasks,
                'myProjects' => $projects,
                'stats' => [
                    'total_tasks' => \App\Models\Task::where('assigned_to', $user->id)->count(),
                    'pending' => \App\Models\Task::where('assigned_to', $user->id)->where('status', 'pending')->count(),
                    'in_progress' => \App\Models\Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
                    'completed' => \App\Models\Task::where('assigned_to', $user->id)->where('status', 'completed')->count(),
                ]
            ]);
        }
    })->name('dashboard');

    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::get('projects-trash', [\App\Http\Controllers\ProjectController::class, 'trash'])->name('projects.trash');
    Route::post('projects/{id}/restore', [\App\Http\Controllers\ProjectController::class, 'restore'])->name('projects.restore');
    Route::delete('projects/{id}/force-delete', [\App\Http\Controllers\ProjectController::class, 'forceDelete'])->name('projects.forceDelete');
    
    Route::post('tasks/{id}/restore', [\App\Http\Controllers\TaskController::class, 'restoreTask'])->name('tasks.restore');
    Route::delete('tasks/{id}/force-delete', [\App\Http\Controllers\TaskController::class, 'forceDeleteTask'])->name('tasks.forceDelete');
    Route::get('tasks/review', function () {
        $projects = \App\Models\Project::all();
        return Inertia::render('Tasks/Review', [
            'projects' => $projects,
        ]);
    })->name('tasks.review');
    Route::post('tasks/{task}/comments', [\App\Http\Controllers\TaskController::class, 'storeComment'])->name('tasks.comments.store');
    Route::post('tasks/{task}/attachments', [\App\Http\Controllers\TaskController::class, 'storeAttachment'])->name('tasks.attachments.store');
    Route::delete('tasks/{task}/attachments/{attachment}', [\App\Http\Controllers\TaskController::class, 'deleteAttachment'])->name('tasks.attachments.destroy');
    Route::post('comments/{comment}/mark-read', [\App\Http\Controllers\TaskController::class, 'markCommentAsRead'])->name('comments.mark-read');
    Route::post('attachments/{attachment}/mark-read', [\App\Http\Controllers\TaskController::class, 'markAttachmentAsRead'])->name('attachments.mark-read');
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);

    Route::get('users', function () {
        return Inertia::render('Users');
    })->name('users');

    Route::get('reports', function () {
        return Inertia::render('Reports');
    })->name('reports');
});

require __DIR__.'/settings.php';
