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
            return response()->json([
                'message' => 'Authorization header is required',
                'return_code' => '-1',
            ], 401);
        } else {

            if (env('AUTH_ENABLE') === true) {
                $authorizationHeader = $request->header('Authorization');
                $tokenParts = explode(' ', $authorizationHeader);

                // Assuming the format is "Bearer <token>", $tokenParts[1] should be your token.
                $token = $tokenParts[1] ?? ''; // Using the null coalescing operator to avoid undefined index errors.


                $response = Http::asForm()->post(env('AUTH_TOKEN_INTROSPECT'), [
                    'client_id' => 'php-authflow-secret',
                    'client_secret' => 'm5ZfaiJAa5YddxoxSJsoS2iR9EYq6jqt',
                    'token' => $token,
                ]);

                $responseArray = $response->json();

                if ($responseArray['active']) {

                    return $next($request);

                } else {

                    return response([
                        'message' => 'Error. Token is not valid.',
                        'return_code' => '-2',
                    ], $response->status());

                }
            } else {
                $request->attributes->add(['preferred_username' => 'auth-off']);
                return $next($request);
            }
        }
    }
}

// This is for user token

// if (env('AUTH_ENABLE') === true) {
//     $token = $request->header('Authorization');

//     $response = Http::withHeaders(['Authorization' => $token])
//         ->get(env('AUTH_USERINFO_URL'));

//     if ($response->successful()) {
//         $keycloakData = $response->json();

//         if (isset($keycloakData['preferred_username'])) {
//             $preferredUsername = $keycloakData['preferred_username'];

//             $request->attributes->add(['preferred_username' => $preferredUsername]);
//         }

//         return $next($request);
//     } else {
//         return response([
//             'message' => 'Error. Token is not valid.',
//             'return_code' => '-2',
//         ], $response->status());
//     }
// } else {
//     $request->attributes->add(['preferred_username' => 'auth-off']);
//     return $next($request);
// }