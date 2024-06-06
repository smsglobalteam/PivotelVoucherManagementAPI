<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TokenController extends Controller
{
    //
    public function tokeninspect(Request $request)
    {

        // $vouchers = Vouchers::get();

        $authorizationHeader = $request->header('Authorization');
        $tokenParts = explode(' ', $authorizationHeader);

        $token = $tokenParts[1] ?? '';

        $response = Http::asForm()->post(env('AUTH_TOKEN_INTROSPECT'), [
            'client_id' => env('AUTH_CONFIDENTIAL_CLIENT_ID'),
            'client_secret' => env('AUTH_CONFIDENTIAL_CLIENT_SECRET'),
            'token' => $token,
        ]);

        $responseArray = $response->json();

        return response([
            'message' => "Keycloak response",
            'introspect' => $responseArray,
        ], 200);
    }
}
