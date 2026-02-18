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
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('requires_file_submission')->default(false);
            $table->boolean('requires_image_submission')->default(false);
            $table->boolean('requires_link_submission')->default(false);
            $table->text('submission_instructions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'requires_file_submission',
                'requires_image_submission', 
                'requires_link_submission',
                'submission_instructions'
            ]);
        });
    }
};
