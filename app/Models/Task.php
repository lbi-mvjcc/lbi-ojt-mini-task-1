<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'project_id',
        'customer_id',
        'assigned_to',
        'title',
        'description',
        'category',
        'status',
        'deadline'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->with('user')->latest();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class)->with('user')->latest();
    }

    // Auto-assign task based on category
    public static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (!$task->assigned_to) {
                $task->assigned_to = self::autoAssignDeveloper($task->project_id, $task->category);
            }
        });
    }

    private static function autoAssignDeveloper($projectId, $category)
    {
        $roleMap = [
            'frontend' => 'frontend_developer',
            'backend' => 'backend_developer',
            'server' => 'server_administrator',
        ];

        $role = $roleMap[$category] ?? null;

        if ($role) {
            // Check if this project already has a developer assigned for this category
            $existingTask = self::where('project_id', $projectId)
                ->where('category', $category)
                ->whereNotNull('assigned_to')
                ->first();

            // If a developer is already assigned to this category in this project, use them
            if ($existingTask) {
                return $existingTask->assigned_to;
            }

            // Otherwise, find the least busy developer with the matching role
            $developer = \App\Models\User::where('role', $role)
                ->withCount(['assignedTasks' => function($query) {
                    $query->whereIn('status', ['pending', 'in_progress']);
                }])
                ->orderBy('assigned_tasks_count', 'asc')
                ->first();

            return $developer?->id;
        }

        return null;
    }
}
