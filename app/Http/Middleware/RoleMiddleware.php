<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Memeriksa apakah user sudah login dan peran sesuai
        if (Auth::check()) {
            if (Auth::user()->role != $role) {
                // Jika role tidak sesuai, redirect ke halaman yang sesuai
                return redirect()->route('login');
            }
        } else {
            // Jika user belum login
            return redirect()->route('login');
        }

        return $next($request);
    }
}
