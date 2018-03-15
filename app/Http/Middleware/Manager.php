<?php

namespace App\Http\Middleware;

use Closure;

class Manager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $consumer = $request->headers->get('x-consumer-username');
        if($consumer !== 'quiz') {
            return response()->json(['error' => 'Feature not allowed'], 403);
        }
        return $next($request);
    }
}
