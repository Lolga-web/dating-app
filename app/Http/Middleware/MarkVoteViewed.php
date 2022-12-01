<?php

namespace App\Http\Middleware;

use App\Models\Voting;
use Closure;
use Illuminate\Http\Request;

class MarkVoteViewed
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
        Voting::where('viewed_by_winner', false)
              ->whereIn('winning_photo', \Auth::user()->getMedia(\Auth::user()->mediaCollection)->pluck('id'))
              ->update(['viewed_by_winner' => true]);
    }
}
