<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user and (if role=parent) create the parent profile.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'role'                  => ['nullable', 'in:admin,staff,parent'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            // Parent profile (created only if role==parent or null)
            'first_name'            => ['nullable', 'string', 'max:100'],
            'last_name'             => ['nullable', 'string', 'max:100'],
        ]);

        $role = $data['role'] ?? 'parent';

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => $role,
        ]);

        if ($role === 'parent') {
            ParentModel::create([
                'user_id'    => $user->id,
                'first_name' => $data['first_name'] ?? $user->name,
                'last_name'  => $data['last_name']  ?? '',
                'phone'      => $data['phone']      ?? '',
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Issue a token for an existing user.
     */
    public function login(Request $request): JsonResponse
    {
        $creds = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $creds['email'])->first();

        if (! $user || ! Hash::check($creds['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * (Optional) Return the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
