<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DefaultResponseBodies;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHesSelf
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (auth()->check()) {

        }

        // If the user is not an admin, you can customize the response or redirect as needed.
        // For example, you can return a forbidden response:
        return DefaultResponseBodies::getUnauthorizedResponse();
    }
}
