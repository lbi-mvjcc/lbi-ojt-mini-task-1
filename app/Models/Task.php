<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'project_id',
        'created_by',
        'assigned_to',
        'status',
        'deadline',
        'requires_file_submission',
        'requires_image_submission',
        'requires_link_submission',
        'submission_instructions',
    ];

    protected $casts = [
        'deadline' => 'date',
        'requires_file_submission' => 'boolean',
        'requires_image_submission' => 'boolean',
        'requires_link_submission' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    /**
     * Get human-readable category label
     */
    public function getCategoryLabel(): string
    {
        $labels = [
            'frontend' => 'Frontend Developer',
            'backend' => 'Backend Developer',
            'server' => 'Server Administrator',
        ];

        return $labels[$this->category] ?? ucfirst($this->category);
    }
        return $this->hasMany(TaskSubmission::class);
    }

    /**
     * Check if task requires any type of submission
     */
    public function requiresSubmissions(): bool
    {
        return $this->requires_file_submission || 
               $this->requires_image_submission || 
               $this->requires_link_submission;
    }

    /**
     * Alias for requiresSubmissions() - Check if task has submission requirements
     */
    public function hasSubmissionRequirements(): bool
    {
        return $this->requiresSubmissions();
    }

    /**
     * Get submissions by assigned user
     */
    public function getSubmissionsForUser($userId)
    {
        return $this->submissions()->where('user_id', $userId)->get();
    }

    /**
     * Check if user has submitted required items
     */
    public function hasRequiredSubmissions($userId): bool
    {
        $userSubmissions = $this->getSubmissionsForUser($userId);
        $submissionTypes = $userSubmissions->pluck('type')->toArray();

        $hasRequired = true;

        if ($this->requires_file_submission && !in_array('file', $submissionTypes)) {
            $hasRequired = false;
        }

        if ($this->requires_image_submission && !in_array('image', $submissionTypes)) {
            $hasRequired = false;
        }

        if ($this->requires_link_submission && !in_array('link', $submissionTypes)) {
            $hasRequired = false;
        }

        return $hasRequired;
    }
}
