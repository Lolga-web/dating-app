<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Guest;
use App\Models\Users\User;

use function App\send_push_notify;

class AddGuest
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
        if ($request->route('user')->id !== auth('api')->user()->id) {
            $isNew = !$request->route('user')->guests()->where([['guest_id', \Auth::id()], ['viewed', false]])->exists();

            Guest::updateOrCreate([
                'user_id' => $request->route('user')->id,
                'guest_id' => auth('api')->user()->id,
            ], [
                'user_id' => $request->route('user')->id,
                'guest_id' => auth('api')->user()->id,
                'visit_at' => now(),
                'viewed' => false,
            ]);

            $user = User::find($request->route('user')->id);
            if ($user->canNotify('guest') && $isNew) {
                send_push_notify(
                    'guest',
                    __('New guest'),
                    \Auth::user()->name . __(' visited your profile'),
                    $user
                );
            }
        }

        return $next($request);
    }
}
