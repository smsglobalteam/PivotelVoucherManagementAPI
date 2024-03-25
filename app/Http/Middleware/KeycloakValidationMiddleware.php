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

                $token = $tokenParts[1] ?? ''; 

                $response = Http::asForm()->post(env('AUTH_TOKEN_INTROSPECT'), [
                    'client_id' => env('AUTH_CONFIDENTIAL_CLIENT_ID'),
                    'client_secret' => env('AUTH_CONFIDENTIAL_CLIENT_SECRET'),
                    'token' => $token,
                ]);

                $responseArray = $response->json();

                if ($responseArray['active']) {

                    // return response([
                    //     'message' => 'Token is valid.',
                    //     'username' => $responseArray['username'],
                    // ], 200);

                    $request->attributes->add(['preferred_username' => $responseArray['username']]);

                    return $next($request);

                } else {

                    return response([
                        'message' => 'Error. Token is not valid.',
                        'return_code' => '-2',
                        // 'response' => $responseArray,
                        // 'col1'=> $authorizationHeader,
                        // 'col2'=> $tokenParts
                    ], $response->status());

                }
            } else {
                $request->attributes->add(['preferred_username' => 'auth-off']);
                // return response([
                //     'message' => 'Off.',
                // ], 200);
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