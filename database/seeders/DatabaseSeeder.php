<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user first
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'password' => bcrypt('admin123'), // Default password
        ]);

        // Create users
        $customer = User::factory()->create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
        ]);
        
    $frontendDevs = User::factory()
        ->frontend()
        ->create();

    $backendDevs = User::factory()
        ->backend()
        ->create();

    $serverAdmins = User::factory()
        ->server()
        ->create();

        // // Create projects for the customer
        // Project::factory()->create([
        //     'name' => 'E-Commerce Website',
        //     'customer_id' => $customer->id,
        // ]);

        // Project::factory()->create([
        //     'name' => 'Mobile App Development',
        //     'customer_id' => $customer->id,
        // ]);

        // Project::factory()->create([
        //     'name' => 'CRM System',
        //     'customer_id' => $customer->id,
        // ]);

        // Project::factory()->create([
        //     'name' => 'Cloud Migration',
        //     'customer_id' => $customer->id,
        // ]);

        // Project::factory()->create([
        //     'name' => 'API Development',
        //     'customer_id' => $customer->id,
        // ]);

        // Project::factory()->create([
        //     'name' => 'Dashboard Analytics',
        //     'customer_id' => $customer->id,
        // ]);
    }
}

