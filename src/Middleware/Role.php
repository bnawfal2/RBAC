<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Role
{
    const DELIMITER = '|';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if (!is_array($roles)) {
			$roles = explode(self::DELIMITER, $roles);
		}
		if (Auth::guest() || !$request->user()->hasRole($roles)) {
			abort(403);
		}
        return $next($request);
    }
}
