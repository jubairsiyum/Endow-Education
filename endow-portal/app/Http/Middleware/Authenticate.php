<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if the request is for student routes
        if ($request->is('student/*') || $request->is('student')) {
            return route('student.login');
        }

        // Default to admin login for admin/employee routes
        return route('admin.login');
    }
}
