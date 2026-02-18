<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckTaskDeadlines extends Command
{
    protected $signature = 'tasks:check-deadlines';
    protected $description = 'Check for tasks that are due soon or overdue and send notifications';

    public function handle()
    {
        $this->info('Checking task deadlines...');

        $today = Carbon::today();
        $dueSoonDate = Carbon::today()->addDays(2); // Tasks due within 2 days
        
        // Find tasks that are due soon (within 2 days) and not completed
        $dueSoonTasks = Task::where('deadline', '>=', $today)
            ->where('deadline', '<=', $dueSoonDate)
            ->whereNotIn('status', ['completed', 'approved'])
            ->whereNotNull('assigned_to')
            ->get();

        // Find overdue tasks (past deadline) and not completed
        $overdueTasks = Task::where('deadline', '<', $today)
            ->whereNotIn('status', ['completed', 'approved'])
            ->whereNotNull('assigned_to')
            ->get();

        $dueSoonCount = 0;
        $overdueCount = 0;

        // Process due soon tasks
        foreach ($dueSoonTasks as $task) {
            // Check if notification already sent for this task today
            $existingNotification = Notification::where('user_id', $task->assigned_to)
                ->where('task_id', $task->id)
                ->where('type', 'task_due_soon')
                ->whereDate('created_at', $today)
                ->first();

            if (!$existingNotification) {
                $daysUntilDue = $today->diffInDays($task->deadline);
                $dueDateText = $task->deadline->format('M j, Y');
                
                if ($daysUntilDue == 0) {
                    $urgencyText = "due today ({$dueDateText})";
                } elseif ($daysUntilDue == 1) {
                    $urgencyText = "due tomorrow ({$dueDateText})";
                } else {
                    $urgencyText = "due in {$daysUntilDue} days ({$dueDateText})";
                }

                Notification::create([
                    'user_id' => $task->assigned_to,
                    'from_user_id' => $task->created_by,
                    'task_id' => $task->id,
                    'type' => 'task_due_soon',
                    'title' => 'Task Due Soon',
                    'message' => "Your task \"{$task->title}\" is {$urgencyText}. Please complete it on time.",
                    'data' => [
                        'task_title' => $task->title,
                        'project_name' => $task->project->name ?? 'Unknown Project',
                        'deadline' => $task->deadline->toISOString(),
                        'days_until_due' => $daysUntilDue,
                        'status' => $task->status
                    ]
                ]);

                $dueSoonCount++;
            }
        }

        // Process overdue tasks
        foreach ($overdueTasks as $task) {
            // Check if notification already sent for this task today
            $existingNotification = Notification::where('user_id', $task->assigned_to)
                ->where('task_id', $task->id)
                ->where('type', 'task_overdue')
                ->whereDate('created_at', $today)
                ->first();

            if (!$existingNotification) {
                $daysOverdue = $task->deadline->diffInDays($today);
                $dueDateText = $task->deadline->format('M j, Y');
                
                if ($daysOverdue == 1) {
                    $overdueText = "1 day overdue (was due {$dueDateText})";
                } else {
                    $overdueText = "{$daysOverdue} days overdue (was due {$dueDateText})";
                }

                Notification::create([
                    'user_id' => $task->assigned_to,
                    'from_user_id' => $task->created_by,
                    'task_id' => $task->id,
                    'type' => 'task_overdue',
                    'title' => 'Task Overdue',
                    'message' => "Your task \"{$task->title}\" is {$overdueText}. Please complete it as soon as possible.",
                    'data' => [
                        'task_title' => $task->title,
                        'project_name' => $task->project->name ?? 'Unknown Project',
                        'deadline' => $task->deadline->toISOString(),
                        'days_overdue' => $daysOverdue,
                        'status' => $task->status
                    ]
                ]);

                $overdueCount++;
            }
        }

        $this->info("Task deadline check completed:");
        $this->info("- Due soon notifications sent: {$dueSoonCount}");
        $this->info("- Overdue notifications sent: {$overdueCount}");

        return 0;
    }
}