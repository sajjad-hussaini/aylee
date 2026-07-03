<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignGuestToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if (!$request->user() && !$request->hasHeader('X-Guest-Token') && !$request->cookie('guest_token')) {
            $request->headers->set('X-Guest-Token', Str::uuid()->toString());
        }

        return $next($request);
    }
}
