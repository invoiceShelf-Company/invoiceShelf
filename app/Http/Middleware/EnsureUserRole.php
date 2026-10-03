<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->role?->value;
        if (! $user || (! $user->isAdmin() && ! in_array($role, $roles, true))) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
