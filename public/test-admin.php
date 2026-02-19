<?php
// Simple test file to check admin status
// Access this at: http://your-site.com/test-admin.php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Check if user is logged in
if (auth()->check()) {
    $user = auth()->user();
    echo "<h1>Admin Status Check</h1>";
    echo "<p><strong>Logged in:</strong> YES</p>";
    echo "<p><strong>User ID:</strong> " . $user->id . "</p>";
    echo "<p><strong>Name:</strong> " . $user->name . "</p>";
    echo "<p><strong>Email:</strong> " . $user->email . "</p>";
    echo "<p><strong>Role:</strong> " . $user->role . "</p>";
    echo "<p><strong>Is Admin:</strong> " . ($user->isAdmin() ? 'YES ✅' : 'NO ❌') . "</p>";
    echo "<p><strong>Role Constant:</strong> " . App\Models\User::ROLE_ADMIN . "</p>";
    
    if (!$user->isAdmin()) {
        echo "<hr>";
        echo "<h2 style='color: red;'>❌ You are NOT an admin!</h2>";
        echo "<p>Your role is: <strong>" . $user->role . "</strong></p>";
        echo "<p>It should be: <strong>admin</strong></p>";
        echo "<h3>Fix it now:</h3>";
        echo "<p>Run this SQL in phpMyAdmin:</p>";
        echo "<pre>UPDATE users SET role = 'admin' WHERE id = " . $user->id . ";</pre>";
        echo "<p>Then LOG OUT and LOG BACK IN!</p>";
    } else {
        echo "<hr>";
        echo "<h2 style='color: green;'>✅ You ARE an admin!</h2>";
        echo "<p>If you're still getting 403 errors, try:</p>";
        echo "<ol>";
        echo "<li>Clear browser cache (Ctrl+Shift+Delete)</li>";
        echo "<li>Log out completely</li>";
        echo "<li>Close browser</li>";
        echo "<li>Open browser and log back in</li>";
        echo "</ol>";
    }
} else {
    echo "<h1>Not Logged In</h1>";
    echo "<p>Please log in first, then visit this page again.</p>";
}
