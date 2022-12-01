<?php

namespace App\Http\Middleware;

use App\Models\UserMatch;
use Closure;
use Illuminate\Http\Request;

class MarkMatchViewed
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
        return $next($request);
    }

    /**
     * Обработка задач после отправки ответа в браузер.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        UserMatch::where([
                'user_id' => \Auth::id(),
                'matched' => true,
                'viewed' => false
            ])
            ->update(['viewed' => true]);
    }
}
