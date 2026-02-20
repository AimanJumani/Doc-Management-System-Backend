<?php

/**
 * Authentication Controller (API v1)
 *
 * Handles all authentication-related endpoints for the Document Management System:
 * - User registration (creates new employee accounts)
 * - User login (authenticates and issues API tokens)
 * - User logout (revokes the current API token)
 * - Get current user profile
 *
 * Authentication uses Laravel Sanctum for token-based API authentication.
 * All new registrations are automatically assigned the "employee" role.
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     *
     * Validates the input, creates a new user, assigns the "employee" role,
     * generates a Sanctum API token, and returns the user data with the token.
     *
     * @param  Request  $request  Contains: name, email, password, password_confirmation, department_id
     * @return JsonResponse  201 on success with user data and auth token
     */
    public function register(Request $request): JsonResponse
    {
        // Validate registration input fields
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        // Create the new user record in the database
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'department_id' => $validated['department_id'],
        ]);

        // All self-registered users get the "employee" role by default
        $user->assignRole('employee');

        // Generate a Sanctum personal access token for API authentication
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user->load('department')),
            'token' => $token,
        ], 201);
    }

    /**
     * Log in an existing user.
     *
     * Validates credentials, revokes any existing tokens (single-session),
     * issues a new Sanctum token, and returns user data with the token.
     *
     * @param  Request  $request  Contains: email, password
     * @return JsonResponse  200 on success, 401 on invalid credentials
     */
    public function login(Request $request): JsonResponse
    {
        // Validate login credentials
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt authentication with provided credentials
        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Revoke all previous tokens to enforce single-session login
        $user->tokens()->delete();

        // Issue a fresh API token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user->load('department')),
            'token' => $token,
        ], 200);
    }

    /**
     * Log out the currently authenticated user.
     *
     * Revokes only the current access token (not all tokens).
     *
     * @param  Request  $request
     * @return JsonResponse  200 on success
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete only the token used for this request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get the currently authenticated user's profile.
     *
     * Returns the user data along with their department information.
     * Used by the frontend to verify the session and display user info.
     *
     * @param  Request  $request
     * @return JsonResponse  User profile data
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()->load('department')),
        ]);
    }
}
