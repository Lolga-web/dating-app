<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetUserOnline
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('api')->check()) {
            $last_online = new Carbon(auth('api')->user()->last_online_at);
            if ($last_online->diffInMinutes(now()) >= 5 || auth('api')->user()->last_online_at == null) {
                DB::table('users')
                    ->where('id', auth('api')->user()->id)
                    ->update(['last_online_at' => now()]);
            }
        }
        return $next($request);
    }
}
