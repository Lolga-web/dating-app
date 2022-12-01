<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Chat;
use Illuminate\Support\Facades\Route;

class MarkMessageRead
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
        Chat::where([
                ['recipient_id', \Auth::id()],
                ['sender_id', Route::current()->parameter('user')->id]
            ])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
