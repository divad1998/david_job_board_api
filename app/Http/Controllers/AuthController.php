<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    function register(RegistrationRequest $request) {
        $user = new User;
        $user->name = $request->name; $user->email = $request->email;
        $user->password = $request->password;
        $user->logo = $request->file('avatar') ? $request->file('avatar')->store('avatars', 'public') : null;
        $user->email_verified_at = now();
        $user->save();

        $user->avatar = $user->logo;
        unset($user->logo);

        return response()->json([
            'status' => 'success', 'message' => "User created successfully!",
            'data' => $user
        ]);
    }

    function login(Request $request) {
        $request->validate([
            'email'    => 'required|string|email|exists:users,email',
            'password' => ['required', Password::min(8)->max(255)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // Create a token for the user/business
        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'token'   => $token
        ]);
    }

    function logout(){
        $user = auth()->user();
        // Revoke all tokens issued to the business
        $user->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out user.'
        ]);
    }
}
