<?php

namespace App\Services\KaisAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class KaisAuth
{
    protected $cookieDomain;

    public function __construct()
    {
        $this->cookieDomain = config('app.cookie_domain');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token])
                ->cookie('api_token', $token, 60 * 24, '/', $this->cookieDomain); // 24 hours
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $token = $request->cookie('api_token');
        $userToken = PersonalAccessToken::findToken($token);
        if ($userToken) {
            $userToken->delete();
            return response()->json(['message' => 'Logged out'])
                ->cookie('api_token', '', -1, '/', $this->cookieDomain);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function user(Request $request)
    {
        $token = $request->cookie('api_token');
        $userToken = PersonalAccessToken::findToken($token);
        if ($userToken) {
            return response()->json($userToken->tokenable);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getCookie(Request $request, $name)
    {
        return $request->cookie($name);
    }
}
