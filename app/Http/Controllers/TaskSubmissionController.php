<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class TaskSubmissionController extends Controller
{
    // Comprehensive list of supported image file extensions
    private const ALLOWED_IMAGE_EXTENSIONS = [
        'jpeg', 'jpg', 'png', 'gif', 'svg', 'webp', 'bmp', 'tiff', 'tif',
        'ico', 'heic', 'heif', 'avif', 'jfif', 'pjpeg', 'pjp', 'apng',
        'raw', 'cr2', 'nef', 'arw', 'dng', 'orf', 'rw2', 'pef', 'srw'
    ];

    // Maximum file sizes in kilobytes
    private const MAX_FILE_SIZE_KB = 102400; // 100MB for general files
    private const MAX_IMAGE_SIZE_KB = 51200;  // 50MB for images

    // Dangerous file extensions that should be blocked for security
    private const BLOCKED_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
        'msi', 'dll', 'app', 'deb', 'pkg', 'ps1', 'sh', 'php', 'asp', 'jsp'
    ];
    /**
     * Display submissions for a task
     */
    public function index(Task $task)
    {
        $user = Auth::user();
        
        // Only show submissions for assigned developers or task creators
        if ($user->isDeveloper() && $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        if ($user->isCustomer() && $task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $submissions = $task->submissions()->with('user')->latest()->get();

        return view('tasks.submissions.index', compact('task', 'submissions'));
    }

    /**
     * Store a new submission
     */
    public function store(Request $request, Task $task)
    {
        $user = Auth::user();
        
        // Only developers assigned to the task can submit
        if (!$user->isDeveloper() || $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $validated = $request->validate([
                'type' => 'required|in:file,image,link',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'file' => 'nullable|file|max:' . self::MAX_FILE_SIZE_KB, // Dynamic max size
                'link_url' => 'nullable|url|max:500',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Submission validation failed', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'errors' => $e->errors(),
                'submission_type' => $request->get('type')
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Enhanced validation based on submission type
        try {
            if ($validated['type'] === 'file') {
                if (!$request->hasFile('file')) {
                    $error = 'File is required for file submissions.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'errors' => ['file' => [$error]]], 422);
                    }
                    return back()->withErrors(['file' => $error]);
                }
                
                // Validate file for general uploads (allow all except dangerous types)
                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                
                if (in_array($extension, self::BLOCKED_EXTENSIONS)) {
                    $error = "File type '{$extension}' is not allowed for security reasons.";
                    Log::warning('Blocked file type upload attempt', [
                        'user_id' => $user->id,
                        'task_id' => $task->id,
                        'extension' => $extension,
                        'filename' => $file->getClientOriginalName()
                    ]);
                    
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'errors' => ['file' => [$error]]], 422);
                    }
                    return back()->withErrors(['file' => $error]);
                }

                // Additional validation for file size
                $request->validate([
                    'file' => 'required|file|max:' . self::MAX_FILE_SIZE_KB
                ]);
            }

            if ($validated['type'] === 'image') {
                if (!$request->hasFile('file')) {
                    $error = 'Image file is required for image submissions.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'errors' => ['file' => [$error]]], 422);
                    }
                    return back()->withErrors(['file' => $error]);
                }

                $file = $request->file('file');
                $extension = strtolower($file->getClientOriginalExtension());
                
                // Check if it's a supported image type
                if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS)) {
                    $supportedTypes = implode(', ', array_slice(self::ALLOWED_IMAGE_EXTENSIONS, 0, 10)) . '...';
                    $error = "Image type '{$extension}' is not supported. Supported formats: {$supportedTypes}";
                    
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'errors' => ['file' => [$error]]], 422);
                    }
                    return back()->withErrors(['file' => $error]);
                }

                // Validate image file with expanded mime types
                $mimeTypes = implode(',', self::ALLOWED_IMAGE_EXTENSIONS);
                $request->validate([
                    'file' => "required|file|mimes:{$mimeTypes}|max:" . self::MAX_IMAGE_SIZE_KB
                ]);
            }

            if ($validated['type'] === 'link' && !$validated['link_url']) {
                $error = 'URL is required for link submissions.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'errors' => ['link_url' => [$error]]], 422);
                }
                return back()->withErrors(['link_url' => $error]);
            }
        } catch (ValidationException $e) {
            Log::error('File validation failed during submission', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'type' => $validated['type'],
                'errors' => $e->errors()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        try {
            $submission = new TaskSubmission();
            $submission->task_id = $task->id;
            $submission->user_id = $user->id;
            $submission->type = $validated['type'];
            $submission->title = $validated['title'];
            $submission->description = $validated['description'];

            // Handle file upload with enhanced error handling
            if ($request->hasFile('file') && ($validated['type'] === 'file' || $validated['type'] === 'image')) {
                $file = $request->file('file');
                
                // Validate file exists and is valid
                if (!$file->isValid()) {
                    throw new Exception('Uploaded file is corrupted or invalid.');
                }

                // Generate secure filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                
                // Ensure the submissions directory exists
                $submissionDir = 'submissions/' . $task->id;
                if (!Storage::disk('public')->exists($submissionDir)) {
                    Storage::disk('public')->makeDirectory($submissionDir);
                }
                
                // Store the file
                $path = $file->storeAs($submissionDir, $filename, 'public');
                
                if (!$path) {
                    throw new Exception('Failed to store uploaded file.');
                }
                
                $submission->file_path = $path;
                $submission->original_filename = $originalName;
                $submission->file_size = $file->getSize();
                $submission->mime_type = $file->getMimeType();
                
                // Auto-generate title from filename if not provided
                if (empty($validated['title'])) {
                    $submission->title = pathinfo($originalName, PATHINFO_FILENAME);
                }
            }

            // Handle link submission
            if ($validated['type'] === 'link') {
                $submission->link_url = $validated['link_url'];
                
                // Auto-generate title from URL if not provided
                if (empty($validated['title'])) {
                    $parsedUrl = parse_url($validated['link_url']);
                    $submission->title = ($parsedUrl['host'] ?? 'Link') . ' - ' . date('M j, Y');
                }
            }

            $submission->save();
            
            Log::info('Submission created successfully', [
                'submission_id' => $submission->id,
                'user_id' => $user->id,
                'task_id' => $task->id,
                'type' => $validated['type'],
                'file_size' => $submission->file_size ?? null
            ]);

            // Create notification for the task creator (customer)
            $customer = $task->createdBy;
            Notification::create([
                'user_id' => $customer->id,
                'from_user_id' => $user->id,
                'task_id' => $task->id,
                'type' => 'submission_uploaded',
                'title' => 'New Submission',
                'message' => "A developer uploaded a new {$validated['type']} submission for task \"{$task->title}\"",
                'data' => [
                    'task_title' => $task->title,
                    'submission_type' => $validated['type'],
                    'submission_title' => $validated['title'],
                    'project_name' => $task->project->name ?? 'Unknown Project'
                ]
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($validated['type']) . ' submission uploaded successfully!',
                    'submission' => [
                        'id' => $submission->id,
                        'type' => $submission->type,
                        'title' => $submission->title,
                        'created_at' => $submission->created_at->format('M j, Y g:i A')
                    ],
                    'redirect_url' => route('tasks.show', $task)
                ]);
            }

            return redirect()->route('tasks.show', $task)->with('success', ucfirst($validated['type']) . ' submission uploaded successfully!');
        } catch (Exception $e) {
            Log::error('Failed to create submission', [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'type' => $validated['type'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up any partially uploaded files
            if (isset($path) && $path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            $errorMessage = 'Failed to upload submission. Please try again.';
            
            // Provide more specific error messages for common issues
            if (str_contains($e->getMessage(), 'file')) {
                $errorMessage = 'There was an issue with the uploaded file. Please check the file and try again.';
            } elseif (str_contains($e->getMessage(), 'size')) {
                $errorMessage = 'The uploaded file is too large. Please choose a smaller file.';
            } elseif (str_contains($e->getMessage(), 'storage')) {
                $errorMessage = 'Storage error occurred. Please contact support if this continues.';
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Delete a submission
     */
    public function destroy(Task $task, TaskSubmission $submission)
    {
        $user = Auth::user();
        
        // Ensure submission belongs to the task
        if ($submission->task_id !== $task->id) {
            abort(404, 'Submission not found for this task');
        }
        
        // Only the submission owner or task creator can delete
        if ($submission->user_id !== $user->id && $submission->task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        try {
            // Delete the file if it exists
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $submission->delete();

            return redirect()->back()->with('success', 'Submission deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete submission: ' . $e->getMessage());
        }
    }

    /**
     * Download a submission file
     */
    public function download(Task $task, TaskSubmission $submission)
    {
        $user = Auth::user();
        
        // Ensure submission belongs to the task
        if ($submission->task_id !== $task->id) {
            abort(404, 'Submission not found for this task');
        }
        
        // Only the submission owner or task creator can download
        if ($submission->user_id !== $user->id && $submission->task->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->original_filename);
    }

    /**
     * Show submission form
     */
    public function create(Task $task)
    {
        $user = Auth::user();
        
        // Only developers assigned to the task can upload submissions
        if (!$user->isDeveloper() || $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Check if task requires submissions
        if (!$task->requiresSubmissions()) {
            return redirect()->route('tasks.show', $task)->with('info', 'This task does not require submissions.');
        }

        return view('tasks.submissions.create', compact('task'));
    }
}
