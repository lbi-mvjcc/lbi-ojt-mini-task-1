<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            // Customers see only their created tasks
            $tasks = Task::with(['project', 'assignedUser'])
                ->where('customer_id', $user->id)
                ->latest()
                ->get();
            
            // Get projects where customer has created tasks
            $projects = Project::whereHas('tasks', function($query) use ($user) {
                $query->where('customer_id', $user->id);
            })->get();
        } else {
            // Developers see only tasks assigned to them
            $tasks = Task::with(['project', 'customer'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->get();
            
            // Get projects where developer is assigned
            $projects = ProjectMember::where('user_id', $user->id)
                ->with('project')
                ->get()
                ->pluck('project');
        }

        return Inertia::render('Tasks', [
            'tasks' => $tasks,
            'projects' => $projects,
            'isCustomer' => $user->isCustomer(),
        ]);
    }

    public function create()
    {
        // Only customers can create tasks
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can create tasks');
        }

        // Only show projects owned by the customer
        $projects = Project::where('customer_id', auth()->id())->get();
        $selectedProjectId = request()->query('project');

        return Inertia::render('Tasks/Create', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
        ]);
    }

    public function store(Request $request)
    {
        // Only customers can create tasks
        if (!auth()->user()->isCustomer()) {
            abort(403, 'Only customers can create tasks');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.category' => 'required|in:frontend,backend,server',
            'tasks.*.deadline' => 'nullable|date|after_or_equal:today',
        ]);

        $createdCount = 0;
        foreach ($validated['tasks'] as $taskData) {
            Task::create([
                'project_id' => $validated['project_id'],
                'customer_id' => auth()->id(),
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'category' => $taskData['category'],
                'deadline' => $taskData['deadline'] ?? null,
            ]);
            $createdCount++;
        }

        $message = $createdCount === 1 
            ? 'Task created and automatically assigned!' 
            : "{$createdCount} tasks created and automatically assigned!";

        return redirect()->route('tasks.index')
            ->with('success', $message);
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        
        // Customers can only see their own tasks
        if ($user->isCustomer() && $task->customer_id !== $user->id) {
            abort(403);
        }
        
        // Developers can only see tasks assigned to them
        if ($user->isDeveloper() && $task->assigned_to !== $user->id) {
            abort(403);
        }

        $task->load(['project', 'customer', 'assignedUser', 'comments', 'attachments']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Developers can only update status
        if ($user->isDeveloper() && $task->assigned_to === $user->id) {
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
            ]);
            
            $task->update($validated);
            return back()->with('success', 'Task status updated!');
        }
        
        // Customers can update title, description, and deadline
        if ($user->isCustomer() && $task->customer_id === $user->id) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'deadline' => 'nullable|date|after_or_equal:today',
            ]);
            
            $task->update($validated);
            return back()->with('success', 'Task updated successfully!');
        }
        
        abort(403, 'Unauthorized');
    }

    public function destroy(Task $task)
    {
        // Only the customer who created the task can delete it
        if (auth()->id() !== $task->customer_id) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    public function storeComment(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Only customer or assigned developer can comment
        if (!($user->isCustomer() && $task->customer_id === $user->id) && 
            !($user->isDeveloper() && $task->assigned_to === $user->id)) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        \App\Models\TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Comment added!');
    }

    public function storeAttachment(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Only assigned developer can upload attachments
        if (!($user->isDeveloper() && $task->assigned_to === $user->id)) {
            abort(403, 'Only the assigned developer can upload attachments');
        }

        $validated = $request->validate([
            'type' => 'required|in:link,file,photo,video',
            'link_url' => 'required_if:type,link|url|max:500',
            'file' => 'required_unless:type,link|file|max:51200', // 50MB max
        ], [
            'file.required_unless' => 'Please select a file to upload.',
            'file.max' => 'File size must not exceed 50MB.',
            'link_url.required_if' => 'Please enter a URL.',
            'link_url.url' => 'Please enter a valid URL.',
        ]);

        if ($validated['type'] === 'link') {
            \App\Models\TaskAttachment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'type' => 'link',
                'name' => $validated['link_url'],
                'url' => $validated['link_url'],
            ]);
        } else {
            $file = $request->file('file');
            
            if (!$file) {
                return back()->withErrors(['file' => 'Please select a file to upload.']);
            }
            
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('task_attachments', $fileName, 'public');

            \App\Models\TaskAttachment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'type' => $validated['type'],
                'name' => $file->getClientOriginalName(),
                'url' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Attachment uploaded successfully!');
    }

    public function deleteAttachment(Task $task, $attachmentId)
    {
        $attachment = \App\Models\TaskAttachment::findOrFail($attachmentId);
        
        // Only the user who uploaded can delete
        if ($attachment->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete file from storage if it's not a link
        if ($attachment->type !== 'link' && \Storage::disk('public')->exists($attachment->url)) {
            \Storage::disk('public')->delete($attachment->url);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully!');
    }

    public function markCommentAsRead($commentId)
    {
        $comment = \App\Models\TaskComment::findOrFail($commentId);
        
        // Only mark as read if the current user is the recipient (not the author)
        if ($comment->user_id !== auth()->id()) {
            $comment->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function markAttachmentAsRead($attachmentId)
    {
        $attachment = \App\Models\TaskAttachment::findOrFail($attachmentId);
        
        // Only mark as read if the current user is the recipient (not the author)
        if ($attachment->user_id !== auth()->id()) {
            $attachment->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function restoreTask($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        // Only customers can restore tasks
        if (!auth()->user()->isCustomer()) {
            abort(403);
        }

        // Check if the customer owns this task
        if ($task->customer_id !== auth()->id()) {
            abort(403);
        }

        $task->restore();

        return redirect()->route('projects.trash')
            ->with('success', 'Task restored successfully!');
    }

    public function forceDeleteTask($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        // Only customers can permanently delete tasks
        if (!auth()->user()->isCustomer()) {
            abort(403);
        }

        // Check if the customer owns this task
        if ($task->customer_id !== auth()->id()) {
            abort(403);
        }

        // Delete attachments if any
        $attachments = \App\Models\TaskAttachment::where('task_id', $task->id)->get();
        foreach ($attachments as $attachment) {
            if ($attachment->type !== 'link' && \Storage::disk('public')->exists($attachment->url)) {
                \Storage::disk('public')->delete($attachment->url);
            }
        }

        $task->forceDelete();

        return redirect()->route('projects.trash')
            ->with('success', 'Task permanently deleted!');
    }
}
