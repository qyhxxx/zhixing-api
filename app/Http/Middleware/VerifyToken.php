<?php

namespace App\Http\Middleware;

use Closure;

class VerifyToken
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
        if (!auth()->setRequest($request)->getToken() || !auth()->user()) {
            return response()->json([
                'code' => 2,
                'message' => '身份验证无效，请登录',
            ], 401);
        }
        return $next($request);
    }
}
