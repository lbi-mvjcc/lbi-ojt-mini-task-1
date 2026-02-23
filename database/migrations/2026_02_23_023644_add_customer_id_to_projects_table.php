<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('description')->constrained('users')->nullOnDelete();
        });
        
        // Migrate existing data: move project_id from users to customer_id in projects
        DB::statement('UPDATE projects p INNER JOIN users u ON u.project_id = p.id SET p.customer_id = u.id WHERE u.role = "customer"');
        
        // Remove project_id from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }

    public function down(): void
    {
        // Add project_id back to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });
        
        // Migrate data back
        DB::statement('UPDATE users u INNER JOIN projects p ON p.customer_id = u.id SET u.project_id = p.id WHERE u.role = "customer"');
        
        // Remove customer_id from projects
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
