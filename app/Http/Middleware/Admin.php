<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        $user_scope = $request->headers->get('x-authenticated-scope');
        if(!in_array('admin', $user_scope)) {
            return response()->json(['error' => 'Feature not allowed'], 403);
        }
        return $next($request);
    }
}
