<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tasks
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Customers can view tasks they created, developers can view tasks assigned to them
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
        return $user->isCustomer(); // Only customers can create tasks
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Customers can update tasks they created, developers can update status only
        if ($user->isCustomer()) {
            return $task->project->customer_id === $user->id;
        }
        
        if ($user->isDeveloper()) {
            return $task->assigned_to === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only customers can delete their own tasks
        if ($user->isCustomer()) {
            return $task->project->customer_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->delete($user, $task);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $this->delete($user, $task);
    }

    /**
     * Determine whether the user can upload submissions to the task.
     */
    public function upload(User $user, Task $task): bool
    {
        // Developers can upload submissions to tasks assigned to them
        if ($user->isDeveloper()) {
            return $task->assigned_to === $user->id;
        }
        
        return false;
    }
}
