<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            // Customers see only their own tasks, without developer info
            $tasks = Task::where('customer_id', $user->id)
                ->with(['project', 'attachments'])
                ->latest()
                ->get()
                ->makeHidden(['assigned_to', 'assignedDeveloper']);
        } else {
            // Developers see only tasks assigned to them, without customer info
            $tasks = Task::where('assigned_to', $user->id)
                ->with(['project', 'attachments'])
                ->latest()
                ->get()
                ->makeHidden(['customer_id', 'customer']);
        }

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'in:frontend,backend,server'],
            'project_id' => ['required', 'exists:projects,id'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // 10MB max per file
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'project_id' => $validated['project_id'],
            'customer_id' => $request->user()->id,
        ]);

        // Automatic assignment based on category
        $task->autoAssign();

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('task_attachments', $fileName, 'public');
                
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $task->load(['project', 'attachments']);

        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        // Access control: customers can only see their own tasks
        if ($user->isCustomer() && $task->customer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Access control: developers can only see tasks assigned to them
        if ($user->isDeveloper() && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->load(['project', 'attachments']);

        // Hide developer information from customers
        if ($user->isCustomer()) {
            $task->makeHidden(['assigned_to', 'assignedDeveloper']);
        }

        // Hide customer information from developers
        if ($user->isDeveloper()) {
            $task->makeHidden(['customer_id', 'customer']);
        }

        return response()->json($task);
    }

    public function updateStatus(Request $request, Task $task)
    {
        if ($task->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        // Only the customer who created the task can update it
        if ($task->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'in:frontend,backend,server'],
            'project_id' => ['required', 'exists:projects,id'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['integer', 'exists:task_attachments,id'],
        ]);

        // Update task details
        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'project_id' => $validated['project_id'],
        ]);

        // Re-assign if category or project changed
        $task->autoAssign();

        // Remove specified attachments
        if ($request->has('remove_attachments')) {
            foreach ($request->remove_attachments as $attachmentId) {
                $attachment = $task->attachments()->find($attachmentId);
                if ($attachment) {
                    // Delete file from storage
                    \Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('task_attachments', $fileName, 'public');
                
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $task->load(['project', 'attachments']);

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        // Only the customer who created the task can delete it
        if ($task->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Soft delete the task
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function trashed(Request $request)
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            // Customers see only their own deleted tasks
            $tasks = Task::onlyTrashed()
                ->where('customer_id', $user->id)
                ->with(['project'])
                ->latest('deleted_at')
                ->get();
        } else {
            // Developers see only deleted tasks that were assigned to them
            $tasks = Task::onlyTrashed()
                ->where('assigned_to', $user->id)
                ->with(['project'])
                ->latest('deleted_at')
                ->get();
        }

        return response()->json($tasks);
    }

    public function restore(Request $request, $id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        // Only the customer who created the task can restore it
        if ($task->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->restore();

        return response()->json(['message' => 'Task restored successfully', 'task' => $task->load(['project', 'attachments'])]);
    }

    public function forceDelete(Request $request, $id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        // Only the customer who created the task can permanently delete it
        if ($task->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete all attachments permanently
        foreach ($task->attachments as $attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $task->forceDelete();

        return response()->json(['message' => 'Task permanently deleted']);
    }
}
