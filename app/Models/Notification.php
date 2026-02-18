<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_user_id',
        'task_id',
        'type',
        'title',
        'message',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    // Notification types
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_TASK_UPDATED = 'task_updated';
    const TYPE_STATUS_UPDATED = 'status_updated';
    const TYPE_TASK_DELETED = 'task_deleted';

    /**
     * Get the user who receives the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who triggered the notification
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the related task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Check if the notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Get time difference for display
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to create notification for task assignment
     */
    public static function createTaskAssignedNotification($task, $fromUser, $toUsers)
    {
        $notifications = [];
        foreach ($toUsers as $toUser) {
            $notifications[] = self::create([
                'user_id' => $toUser->id,
                'from_user_id' => $fromUser->id,
                'task_id' => $task->id,
                'type' => self::TYPE_TASK_ASSIGNED,
                'title' => 'New Task Assigned',
                'message' => "You have been assigned a new {$task->category} task: \"{$task->title}\"",
                'data' => [
                    'task_title' => $task->title,
                    'task_category' => $task->category,
                    'project_name' => $task->project->name ?? 'Unknown Project'
                ]
            ]);
        }
        return $notifications;
    }

    /**
     * Static method to create notification for task updates
     */
    public static function createTaskUpdatedNotification($task, $fromUser, $toUsers)
    {
        $notifications = [];
        foreach ($toUsers as $toUser) {
            $notifications[] = self::create([
                'user_id' => $toUser->id,
                'from_user_id' => $fromUser->id,
                'task_id' => $task->id,
                'type' => self::TYPE_TASK_UPDATED,
                'title' => 'Task Updated',
                'message' => "Task \"{$task->title}\" has been updated",
                'data' => [
                    'task_title' => $task->title,
                    'task_category' => $task->category,
                    'project_name' => $task->project->name ?? 'Unknown Project'
                ]
            ]);
        }
        return $notifications;
    }

    /**
     * Static method to create notification for status updates
     */
    public static function createStatusUpdatedNotification($task, $fromUser, $toUser, $oldStatus, $newStatus)
    {
        return self::create([
            'user_id' => $toUser->id,
            'from_user_id' => $fromUser->id,
            'task_id' => $task->id,
            'type' => self::TYPE_STATUS_UPDATED,
            'title' => 'Task Status Updated',
            'message' => "{$fromUser->getRoleLabel()} updated task \"{$task->title}\" from \"{$oldStatus}\" to \"{$newStatus}\"",
            'data' => [
                'task_title' => $task->title,
                'task_category' => $task->category,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'project_name' => $task->project->name ?? 'Unknown Project'
            ]
        ]);
    }

    /**
     * Static method to create notification for task deletion
     */
    public static function createTaskDeletedNotification($task, $fromUser, $toUsers)
    {
        $notifications = [];
        foreach ($toUsers as $toUser) {
            $notifications[] = self::create([
                'user_id' => $toUser->id,
                'from_user_id' => $fromUser->id,
                'task_id' => $task->id,
                'type' => self::TYPE_TASK_DELETED,
                'title' => 'Task Deleted',
                'message' => "Task \"{$task->title}\" has been deleted",
                'data' => [
                    'task_title' => $task->title,
                    'task_category' => $task->category,
                    'project_name' => $task->project->name ?? 'Unknown Project'
                ]
            ]);
        }
        return $notifications;
    }
}