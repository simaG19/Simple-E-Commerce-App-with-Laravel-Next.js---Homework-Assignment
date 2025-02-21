<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    // Register new user
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // password_confirmation field is required
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // hash the password
        ]);

        // Generate an API token for the user
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return the response with the created user and token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201); // Respond with 201 status code (Created)
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if the user exists and if password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create an API token for the user after login
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Debug: Check if token was created
        if (!$token) {
            return response()->json(['message' => 'Token creation failed'], 500);
        }

        return response()->json([
            'user' => $user,
            'token' => $token, // Include token in the response
        ]);
    }


    // Logout user
    public function logout(Request $request)
    {
        // Revoke all API tokens for the authenticated user
        $request->user()->tokens->each(function ($token) {
            $token->delete(); // Revoke all tokens
        });

        return response()->json(['message' => 'Logged out successfully']);
    }
}
