<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'User authenticated successfully',
                'user' => Auth::user(),
                'token' => Auth::user()->createToken('authToken')->plainTextToken
            ]);
        }
        return response()->json(['email' => 'The provided credentials do not match our records.'], 401);
    }
}
