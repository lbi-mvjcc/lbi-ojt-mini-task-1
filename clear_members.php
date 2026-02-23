<?php
// Temporary script to clear project_members table
// Run this once then delete it

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear all project members
DB::table('project_members')->truncate();

echo "Project members table cleared successfully!\n";
echo "You can now add members again.\n";
