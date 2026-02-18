<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Manual command to check task deadlines (for testing)
Artisan::command('tasks:check-deadlines-now', function () {
    $this->info('Running task deadline check...');
    Artisan::call('tasks:check-deadlines');
    $this->info('Task deadline check completed!');
})->purpose('Manually check task deadlines and send notifications');
