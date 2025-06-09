<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Only check admin role if trying to access admin routes
        if (str_starts_with($request->path(), 'adminspage')) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                return redirect()->route('weekplan');
            }
        }

        return $next($request);
    }
}
