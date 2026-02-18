<?php

namespace App\Policies;

use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view submissions
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskSubmission $taskSubmission): bool
    {
        $task = $taskSubmission->task;
        
        // Customers can view submissions for their tasks, developers can view submissions for tasks assigned to them
        if ($user->isCustomer()) {
            return $task->project->customer_id === $user->id;
        }
        
        if ($user->isDeveloper()) {
            return $task->assigned_to === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isDeveloper(); // Only developers can create submissions
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskSubmission $taskSubmission): bool
    {
        // Only the user who uploaded the submission can update it
        return $taskSubmission->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskSubmission $taskSubmission): bool
    {
        // Only the user who uploaded the submission can delete it
        return $taskSubmission->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskSubmission $taskSubmission): bool
    {
        return $this->delete($user, $taskSubmission);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskSubmission $taskSubmission): bool
    {
        return $this->delete($user, $taskSubmission);
    }
}
