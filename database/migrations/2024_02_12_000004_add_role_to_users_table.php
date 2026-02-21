<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'frontend_developer', 'backend_developer', 'server_admin', 'admin'])
                ->default('customer')
                ->after('email');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['role', 'project_id']);
        });
    }
};
