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
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
