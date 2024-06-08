<?php

namespace App\Http\Middleware;
use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon; // If you use Carbon for date comparison
use Illuminate\Support\Facades\Auth;


class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Extract token from the Authorization header
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s0\|(\S+)/', $authHeader, $matches)) {
            return response('Unauthenticated', 401);
        }

        $token = $matches[1];
      // Check the token against the database
        $personalAccessToken = PersonalAccessToken::where('token', hash('sha256', $token))->first();
        if (!$personalAccessToken) {
            return response('Unauthenticated2', 401);
        }

        // Check if the token is expired
        if (Carbon::parse($personalAccessToken->expires_at)->isPast()) {
            return response('Token expired3', 401);
        }

        // Set the authenticated user
        $user = $personalAccessToken->tokenable;
        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // If everything is okay, proceed with the request
        return $next($request);

    }
}
