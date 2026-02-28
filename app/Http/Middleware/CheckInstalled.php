<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstalled
{
    public function handle(Request $request, Closure $next)
    {
        // If app is not installed, redirect to installer
        if (!file_exists(storage_path('installed'))) {
            // Use file sessions during installation (DB may not be ready)
            config(['session.driver' => 'file']);

            if (!$request->is('install*')) {
                return redirect('/install');
            }
        }

        return $next($request);
    }
}
