<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class KeycloakValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['message' => 'Authorization header is required'], 401);
        } else {

            if (env('AUTH_ENABLE') === true) {
                $token = $request->header('Authorization');

                $response = Http::withHeaders(['Authorization' => $token])
                    ->get(env('AUTH_USERINFO_URL'));

                if ($response->successful()) {
                    $keycloakData = $response->json();

                    if (isset($keycloakData['preferred_username'])) {
                        $preferredUsername = $keycloakData['preferred_username'];

                        $request->attributes->add(['preferred_username' => $preferredUsername]);
                    }

                    return $next($request);
                } else {
                    return response([
                        'message' => 'Error. Token is not valid.',
                        'keycloak_response' => $response->json(),
                        'token' => $token,
                    ], $response->status());
                }
            } else {
                $request->attributes->add(['preferred_username' => 'auth-off']);
                return $next($request);
            }
        }
    }
}
