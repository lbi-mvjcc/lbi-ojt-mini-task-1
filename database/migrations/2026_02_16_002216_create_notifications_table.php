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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who receives the notification
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade'); // Who triggered the notification
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Related task
            $table->string('type'); // 'task_assigned', 'task_updated', 'status_updated', 'task_deleted'
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message
            $table->json('data')->nullable(); // Additional data (status changes, etc.)
            $table->timestamp('read_at')->nullable(); // When was it read
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'read_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
