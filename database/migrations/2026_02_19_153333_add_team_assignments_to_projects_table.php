<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Add team member assignments - exactly 1 of each role per project
            $table->foreignId('frontend_developer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('backend_developer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('server_admin_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['frontend_developer_id']);
            $table->dropForeign(['backend_developer_id']);
            $table->dropForeign(['server_admin_id']);
            $table->dropColumn(['frontend_developer_id', 'backend_developer_id', 'server_admin_id']);
        });
    }
};
