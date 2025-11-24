<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated using the admin guard
        if (!auth()->guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'You must be logged in as an admin to access this area.');
        }

        return $next($request);
    }
}
