<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PasswordResetController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Password Reset
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Profile update (all authenticated users)
    Route::post('/users/profile', [AuthController::class, 'updateProfile']);
    
    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::get('/projects/{project}/members', [ProjectController::class, 'members']);
    
    // Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    
    // Recently deleted tasks (must come before {task} route)
    Route::get('/tasks/trashed/all', [TaskController::class, 'trashed']);
    
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    
    // Customer only
    Route::middleware('role:customer')->group(function () {
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
        
        // Restore and force delete for trashed tasks
        Route::post('/tasks/{id}/restore', [TaskController::class, 'restore']);
        Route::delete('/tasks/{id}/force', [TaskController::class, 'forceDelete']);
    });
    
    // Developer only
    Route::middleware('role:frontend_developer,backend_developer,server_admin')->group(function () {
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    });
    
    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/stats', [AdminController::class, 'stats']);
        
        // User management
        Route::get('/admin/users', [AdminController::class, 'getUsers']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);
        
        // Recently deleted users
        Route::get('/admin/users/trashed/all', [AdminController::class, 'getTrashedUsers']);
        Route::post('/admin/users/{id}/restore', [AdminController::class, 'restoreUser']);
        Route::delete('/admin/users/{id}/force', [AdminController::class, 'forceDeleteUser']);
        
        // Project management
        Route::get('/admin/projects', [AdminController::class, 'getProjects']);
        Route::post('/admin/projects', [AdminController::class, 'createProject']);
        Route::put('/admin/projects/{project}', [AdminController::class, 'updateProject']);
        Route::delete('/admin/projects/{project}', [AdminController::class, 'deleteProject']);
        
        // Recently deleted projects
        Route::get('/admin/projects/trashed/all', [AdminController::class, 'getTrashedProjects']);
        Route::post('/admin/projects/{id}/restore', [AdminController::class, 'restoreProject']);
        Route::delete('/admin/projects/{id}/force', [AdminController::class, 'forceDeleteProject']);
        
        // Project members
        Route::get('/admin/projects/{project}/members', [AdminController::class, 'getProjectMembers']);
        Route::post('/admin/projects/{project}/members', [AdminController::class, 'addProjectMember']);
        Route::delete('/admin/projects/{project}/members/{user}', [AdminController::class, 'removeProjectMember']);
        
        // All tasks overview
        Route::get('/admin/tasks', [AdminController::class, 'getAllTasks']);
    });
});
