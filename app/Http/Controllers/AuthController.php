<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|integer|unique:users',
            'type_document' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'document' => $request->document,
            'type_document' => $request->type_document,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json(compact('user'), 200);
    }

    // Authenticate a user and return a JWT
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json(compact('token'), 200);
    }

    // Get the authenticated user
    public function me()
    {
        return response()->json(Auth::user());
    }

    // Log out the user (invalidate the token)
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    // Refresh a token
    public function refresh()
    {
        $token = Auth::refresh();

        return response()->json(compact('token'));
    }
}

