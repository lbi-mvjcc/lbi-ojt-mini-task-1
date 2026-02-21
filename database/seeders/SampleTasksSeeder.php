<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class SampleTasksSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        $project = Project::first();

        if (!$customer || !$project) {
            $this->command->error('No customer or project found. Please run TaskManagementSeeder first.');
            return;
        }

        $tasks = [
            [
                'title' => 'Fix login bug',
                'description' => 'Users are unable to login with correct credentials. Need to investigate authentication flow.',
                'status' => 'pending',
                'category' => 'backend'
            ],
            [
                'title' => 'Update homepage design',
                'description' => 'Redesign the homepage with new branding guidelines and modern UI elements.',
                'status' => 'in_progress',
                'category' => 'frontend'
            ],
            [
                'title' => 'Add payment gateway',
                'description' => 'Integrate Stripe payment processing for subscription management.',
                'status' => 'pending',
                'category' => 'backend'
            ],
            [
                'title' => 'Optimize database queries',
                'description' => 'Improve performance of slow queries in the reporting module.',
                'status' => 'completed',
                'category' => 'backend'
            ],
            [
                'title' => 'Create API documentation',
                'description' => 'Document all API endpoints with examples and response formats.',
                'status' => 'in_progress',
                'category' => 'backend'
            ],
            [
                'title' => 'Setup SSL certificates',
                'description' => 'Configure SSL certificates for production environment.',
                'status' => 'pending',
                'category' => 'server'
            ],
            [
                'title' => 'Implement responsive navigation',
                'description' => 'Make the navigation menu work properly on mobile devices.',
                'status' => 'completed',
                'category' => 'frontend'
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'category' => $taskData['category'],
                'project_id' => $project->id,
                'customer_id' => $customer->id,
            ]);
        }

        $this->command->info('✓ Created 7 sample tasks');
        $this->command->info('Total tasks: ' . Task::count());
    }
}
