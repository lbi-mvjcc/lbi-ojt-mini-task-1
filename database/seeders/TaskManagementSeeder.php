<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskManagementSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [];
        for ($i = 1; $i <= 5; $i++) {
            $projects[] = Project::create([
                'name' => "Project $i",
                'description' => "Description for Project $i",
                'is_active' => true,
            ]);
        }

        foreach ($projects as $index => $project) {
            $frontend = User::create([
                'name' => "Frontend Dev " . ($index + 1),
                'email' => "frontend{$index}@example.com",
                'password' => Hash::make('password'),
                'role' => 'frontend_developer',
            ]);

            $backend = User::create([
                'name' => "Backend Dev " . ($index + 1),
                'email' => "backend{$index}@example.com",
                'password' => Hash::make('password'),
                'role' => 'backend_developer',
            ]);

            $server = User::create([
                'name' => "Server Admin " . ($index + 1),
                'email' => "server{$index}@example.com",
                'password' => Hash::make('password'),
                'role' => 'server_admin',
            ]);

            $project->members()->attach($frontend->id, ['role' => 'frontend_developer']);
            $project->members()->attach($backend->id, ['role' => 'backend_developer']);
            $project->members()->attach($server->id, ['role' => 'server_admin']);
        }

        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Customer $i",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'project_id' => $projects[0]->id,
            ]);
        }

        // Create admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->command->info('✓ Created 5 projects with development teams');
        $this->command->info('✓ Created 3 sample customers');
        $this->command->info('✓ Created 1 admin user');
        $this->command->info('');
        $this->command->info('Login credentials (password: password):');
        $this->command->info('  Admin: admin@example.com');
        $this->command->info('  Customer: customer1@example.com');
        $this->command->info('  Frontend: frontend0@example.com');
        $this->command->info('  Backend: backend0@example.com');
        $this->command->info('  Server: server0@example.com');
    }
}
