<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING EXISTING PROJECT ASSIGNMENTS ===\n\n";

// Get all projects
$projects = DB::table('projects')->get();

foreach ($projects as $project) {
    echo "Processing Project: {$project->name} (ID: {$project->id})\n";
    
    // Get all tasks for this project grouped by category
    $tasks = DB::table('tasks')
        ->where('project_id', $project->id)
        ->get();
    
    $updates = [];
    
    // For each category, find the most common assigned developer
    foreach (['frontend', 'backend', 'server'] as $category) {
        $categoryTasks = $tasks->where('category', $category);
        
        if ($categoryTasks->count() > 0) {
            // Get the most frequently assigned developer for this category
            $developerCounts = [];
            foreach ($categoryTasks as $task) {
                if (!isset($developerCounts[$task->assigned_to])) {
                    $developerCounts[$task->assigned_to] = 0;
                }
                $developerCounts[$task->assigned_to]++;
            }
            
            // Get the developer with most tasks
            arsort($developerCounts);
            $mostCommonDeveloper = array_key_first($developerCounts);
            
            $fieldMap = [
                'frontend' => 'frontend_developer_id',
                'backend' => 'backend_developer_id',
                'server' => 'server_admin_id',
            ];
            
            $updates[$fieldMap[$category]] = $mostCommonDeveloper;
            
            $devName = DB::table('users')->where('id', $mostCommonDeveloper)->value('name');
            echo "   {$category}: Assigned to {$devName} (ID: {$mostCommonDeveloper}) - {$categoryTasks->count()} task(s)\n";
            
            // Update all tasks in this category to use the same developer
            DB::table('tasks')
                ->where('project_id', $project->id)
                ->where('category', $category)
                ->update(['assigned_to' => $mostCommonDeveloper]);
        }
    }
    
    // Update the project with team assignments
    if (!empty($updates)) {
        DB::table('projects')
            ->where('id', $project->id)
            ->update($updates);
        echo "   ✓ Project team updated\n";
    }
    
    echo "\n";
}

echo "=== DONE ===\n";
