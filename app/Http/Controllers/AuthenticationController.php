<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response([
            'message' => "User registered successfully",
            'results' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            // Authentication successful
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken;

            return response()->json([
                'message' => 'Logged in successfully',
                'access_token' => $token,
                'user' => $user,
            ]);
        }

        return response([
            'message' => "Invalid credentials",
        ], 400);

    }

    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function getCurrentUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            // abort(code: 404, message: 'Module not found!');
            abort(
                response()->json([
                    'message' => "Fetching error!",
                ], 404)
            );
        }

        return response([
            'message' => "User displayed successfully",
            'results' => $user
        ], 200);
    }
}
