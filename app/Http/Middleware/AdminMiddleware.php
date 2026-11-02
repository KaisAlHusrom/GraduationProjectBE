<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DefaultResponseBodies;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param \Closure(Request): (Response) $next
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|mixed
     */
    public function handle($request, Closure $next): mixed
    {
        // Check if the user is authenticated
        if (auth()->check()) {
            // Check if the user is an admin (is_admin is true)
            if (auth()->user()->is_admin) {
                // Allow access to the requested route
                return $next($request);
            }
        }

        // If the user is not an admin, you can customize the response or redirect as needed.
        // For example, you can return a forbidden response:
        return DefaultResponseBodies::getUnauthorizedResponse();
    }
}
