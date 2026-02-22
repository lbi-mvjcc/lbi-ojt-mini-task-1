<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->with(['fromUser', 'task.project'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for AJAX requests)
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->unread()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown)
     */
    public function recent()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->with(['fromUser', 'task.project'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $unreadCount = $user->notifications()->unread()->count();
        
        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->isRead(),
                    'time_ago' => $notification->time_ago,
                    'from_user' => $notification->fromUser ? $notification->fromUser->name : 'System',
                    'task_id' => $notification->task_id,
                    'data' => $notification->data
                ];
            }),
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        try {
            $user = Auth::user();
            
            // Ensure the notification belongs to the current user
            if ($notification->user_id !== $user->id) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                return redirect()->back()->with('error', 'Unauthorized.');
            }
            
            $notification->markAsRead();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Notification marked as read']);
            }
            
            return redirect()->back()->with('success', 'Notification marked as read successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to mark notification as read'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to mark notification as read.');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $user->notifications()->unread()->update(['read_at' => now()]);
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
            }
            
            return redirect()->back()->with('success', 'All notifications marked as read successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to mark all notifications as read'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to mark all notifications as read.');
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        try {
            $user = Auth::user();
            
            // Ensure the notification belongs to the current user
            if ($notification->user_id !== $user->id) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                return redirect()->back()->with('error', 'Unauthorized.');
            }
            
            $notification->delete();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Notification deleted successfully']);
            }
            
            return redirect()->back()->with('success', 'Notification deleted successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete notification'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete notification.');
        }
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        try {
            $user = Auth::user();
            
            // Check how many notifications exist first
            $notificationCount = $user->notifications()->count();
            
            if ($notificationCount === 0) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'No notifications to delete']);
                }
                
                return redirect()->back()->with('info', 'No notifications to delete.');
            }
            
            // Delete notifications and get the count
            $deletedCount = $user->notifications()->delete();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => "Successfully deleted {$deletedCount} notification(s)"]);
            }
            
            return redirect()->back()->with('success', "Successfully deleted {$deletedCount} notification(s).");
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete all notifications'], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete all notifications.');
        }
    }

    /**
     * Show a specific notification and mark it as read
     */
    public function show(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure the notification belongs to the current user
        if ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        // Load relationships
        $notification->load(['fromUser', 'task.project']);
            
        // Mark as read if not already read
        if (!$notification->isRead()) {
            $notification->markAsRead();
        }
        
        // Redirect to the related task if it exists
        if ($notification->task_id && $notification->task) {
            return redirect()->route('tasks.show', $notification->task_id)
                ->with('notification_viewed', 'Viewing task from notification');
        }
        
        // Otherwise show notification details
        return view('notifications.show', compact('notification'));
    }
}