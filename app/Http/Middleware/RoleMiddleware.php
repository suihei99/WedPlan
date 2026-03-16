<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , string ...$roles): Response
    {
        $user = $request->user();

        // Not authenticated
        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }
        
        // validation role is a known role
        if (! in_array($user->role, User::ROLES)) {
            abort(403, 'Invalid user role.');
        }

        if(! in_array($user->role, $roles)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Forbidden. Insufficient permissions.'], 403)
                : abort(403, 'Forbidden. Insufficient permissions.');
        }
        
        return $next($request);
    }
}
