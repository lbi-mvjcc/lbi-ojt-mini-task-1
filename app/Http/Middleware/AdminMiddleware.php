<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!auth()->check()) {
            abort(403, 'Unauthorized - You must be logged in');
        }
        
        $user = auth()->user();
        
        // Check if user is admin
        if (!$user->isAdmin()) {
            // Provide detailed error message for debugging
            $message = "Unauthorized - Admin access only. Your role is '{$user->role}' but it should be 'admin'. ";
            $message .= "Please update your role in the database and log out/in again.";
            abort(403, $message);
        }

        return $next($request);
    }
}
