<?php

namespace Cosmos\Rbac\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
    const DELIMITER = '|';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if (!is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }
        if (Auth::guest() || !$request->user()->hasPermission($permissions)) {
            abort(403);
        }
        return $next($request);
    }
}
