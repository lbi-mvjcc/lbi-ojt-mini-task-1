<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskSubmissionController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::get('/tasks/dashboard/developer-workload', [TaskController::class, 'developerWorkload'])->name('tasks.developer-workload');
    Route::get('/search', [TaskController::class, 'search'])->name('search');
    
    // Task Submission routes
    Route::get('/tasks/{task}/submissions', [TaskSubmissionController::class, 'index'])->name('tasks.submissions.index');
    Route::get('/tasks/{task}/submissions/create', [TaskSubmissionController::class, 'create'])->name('tasks.submissions.create');
    Route::post('/tasks/{task}/submissions', [TaskSubmissionController::class, 'store'])->name('tasks.submissions.store');
    Route::get('/tasks/{task}/submissions/{submission}/download', [TaskSubmissionController::class, 'download'])->name('tasks.submissions.download');
    Route::delete('/tasks/{task}/submissions/{submission}', [TaskSubmissionController::class, 'destroy'])->name('tasks.submissions.destroy');
    
    // Projects route
    Route::get('/projects', [TaskController::class, 'projects'])->name('projects.index');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/destroy-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Manual task deadline check (for customers only)
    Route::post('/tasks/check-deadlines', function () {
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Unauthorized');
        }
        
        Artisan::call('tasks:check-deadlines');
        return redirect()->back()->with('success', 'Task deadline check completed successfully! Notifications have been sent to developers with approaching or overdue tasks.');
    })->name('tasks.check-deadlines')->middleware('auth');
    
    // Debug route to check admin status (remove this after testing)
    Route::get('/check-admin', function () {
        $user = auth()->user();
        return response()->json([
            'logged_in' => auth()->check(),
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? null,
            'user_email' => $user->email ?? null,
            'user_role' => $user->role ?? null,
            'is_admin' => $user ? $user->isAdmin() : false,
            'role_constant' => \App\Models\User::ROLE_ADMIN,
        ]);
    })->middleware('auth')->name('check.admin');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Task management
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
    Route::delete('/tasks/{task}', [AdminController::class, 'deleteTask'])->name('tasks.delete');
    
    // Project management
    Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
    Route::delete('/projects/{project}', [AdminController::class, 'deleteProject'])->name('projects.delete');
});

require __DIR__.'/auth.php';
