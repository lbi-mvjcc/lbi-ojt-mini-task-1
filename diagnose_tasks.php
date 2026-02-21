<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC REPORT ===\n\n";

// 1. Check all frontend developers
echo "1. FRONTEND DEVELOPERS:\n";
$frontendDevs = DB::table('users')->where('role', 'frontend_dev')->get(['id', 'name', 'email']);
foreach ($frontendDevs as $dev) {
    echo "   ID: {$dev->id}, Name: {$dev->name}, Email: {$dev->email}\n";
}
echo "\n";

// 2. Check all projects and their team assignments
echo "2. PROJECTS AND TEAM ASSIGNMENTS:\n";
$projects = DB::table('projects')
    ->leftJoin('users as fe', 'projects.frontend_developer_id', '=', 'fe.id')
    ->leftJoin('users as be', 'projects.backend_developer_id', '=', 'be.id')
    ->leftJoin('users as sa', 'projects.server_admin_id', '=', 'sa.id')
    ->select(
        'projects.id',
        'projects.name',
        'projects.frontend_developer_id',
        'fe.name as fe_name',
        'projects.backend_developer_id',
        'be.name as be_name',
        'projects.server_admin_id',
        'sa.name as sa_name'
    )
    ->get();

foreach ($projects as $project) {
    echo "   Project: {$project->name} (ID: {$project->id})\n";
    echo "      Frontend Dev: " . ($project->fe_name ?? 'NONE') . " (ID: " . ($project->frontend_developer_id ?? 'NULL') . ")\n";
    echo "      Backend Dev: " . ($project->be_name ?? 'NONE') . " (ID: " . ($project->backend_developer_id ?? 'NULL') . ")\n";
    echo "      Server Admin: " . ($project->sa_name ?? 'NONE') . " (ID: " . ($project->server_admin_id ?? 'NULL') . ")\n";
    echo "\n";
}

// 3. Check all frontend tasks
echo "3. ALL FRONTEND TASKS:\n";
$frontendTasks = DB::table('tasks')
    ->join('projects', 'tasks.project_id', '=', 'projects.id')
    ->join('users as assigned', 'tasks.assigned_to', '=', 'assigned.id')
    ->join('users as creator', 'tasks.created_by', '=', 'creator.id')
    ->where('tasks.category', 'frontend')
    ->select(
        'tasks.id',
        'tasks.title',
        'projects.name as project_name',
        'tasks.assigned_to',
        'assigned.name as assigned_name',
        'creator.name as creator_name',
        'tasks.created_at'
    )
    ->orderBy('tasks.created_at', 'desc')
    ->get();

foreach ($frontendTasks as $task) {
    echo "   Task: {$task->title} (ID: {$task->id})\n";
    echo "      Project: {$task->project_name}\n";
    echo "      Assigned To: {$task->assigned_name} (ID: {$task->assigned_to})\n";
    echo "      Created By: {$task->creator_name}\n";
    echo "      Created At: {$task->created_at}\n";
    echo "\n";
}

// 4. Count tasks per developer
echo "4. TASK COUNT PER FRONTEND DEVELOPER:\n";
foreach ($frontendDevs as $dev) {
    $count = DB::table('tasks')
        ->where('assigned_to', $dev->id)
        ->where('category', 'frontend')
        ->count();
    echo "   {$dev->name}: {$count} task(s)\n";
}

echo "\n=== END OF REPORT ===\n";
