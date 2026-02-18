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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Developer who submitted
            $table->enum('type', ['file', 'image', 'link']); // Type of submission
            $table->string('title')->nullable(); // Optional title for the submission
            $table->text('description')->nullable(); // Optional description
            $table->string('file_path')->nullable(); // Path to uploaded file/image
            $table->string('original_filename')->nullable(); // Original filename
            $table->string('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable(); // File mime type
            $table->text('link_url')->nullable(); // URL for link submissions
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['task_id', 'type']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
