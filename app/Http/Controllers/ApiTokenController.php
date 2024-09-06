<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ApiTokenController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('YourAppName')->plainTextToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        // Ensure the request is authenticated
        $user = $request->user();

        if ($user) {
            // Revoke all tokens for the user
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|confirmed|min:8',
    ]);

    // Return validation errors if any
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Create a new user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Create a token with a name
    $token = $user->createToken('Registration Token')->plainTextToken;

    // Return user data and token
    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
}

}
