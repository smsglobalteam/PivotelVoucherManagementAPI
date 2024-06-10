<?php

namespace App\Http\Middleware;

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
    public function handle($request, Closure $next, ...$roles)
    {
        if (env('AUTH_ENABLE') === true) {
            $userRoles = $request->attributes->get('user_role')['roles'] ?? null;

            if (is_null($userRoles) || empty($userRoles)) {
                return response([
                    'message' => 'Access error. User does not have roles assigned for this operation',
                    'return_code' => '-3',
                ], 403);
            }

            foreach ($roles as $role) {
                if (in_array($role, $userRoles)) {
                    return $next($request);
                }
            }

            return response([
                'message' => 'Access error. User does not have roles assigned for this operation',
                'return_code' => '-4',
                'roles' => $userRoles,
            ], 403);
        } else {
            return $next($request);
        }
    }
}
